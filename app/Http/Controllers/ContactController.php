<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Contact;
use \App\Repositories\Air;
use \App\Zipcode;
use \App\Repositories\ZipcodeRepository;

class ContactController extends Controller
{
    
    /*
     * Handle the web input and output
     *   It is the responsible of calling the match algorithm
    */
    function getByClosestZipCode($zipcodes)
    {
        // This function assumes all the agents zip codes has been already cached
        //  TODO: Don't assume!!
        
        $cachedZipCodes = Air::get('cached-zip-codes', function() {
            return Zipcode::all()->keyBy('zipcode')->all();
        });
        
        $zipcodes = explode(',', $zipcodes);
        $answer = [];
        
        // Validates the input has two zip codes
        if( count($zipcodes) == 2 ) {
        
            // Call the match algorithm!!
            // Here is where the magic happens
            $contacts = Contact::all()->all();
            $match = $this->_matchClosest($contacts, $zipcodes[0], $zipcodes[1]);
            
            if( ISSET($match['status']) && $match['status'] == 'error' ) {
                $answer = $match;             
            } else {                
                
                // If the match was correct, build the answer                
                $clasifiedContacts = [];
                $agents = ['A', 'B'];
                foreach($agents as $agent) {
                    foreach($match[$agent] as $contact) {
                        $clasifiedContacts[] = [
                            'agent' => [
                                'id' => $agent
                            ],
                            'contact' => $contact,
                            'location' => $cachedZipCodes[$contact->zipcode]
                        ];
                    }    
                }                   
                $answer = [
                    'status' => 'success',
                    'data' => [
                        'contacts' => $clasifiedContacts,
                        'agents' => [
                            'A' => [
                                'id' => 'A',
                                'location' => ZipcodeRepository::zipcodeToLatLng($zipcodes[0])
                            ],
                            'B' => [
                                'id' => 'A',
                                'location' => ZipcodeRepository::zipcodeToLatLng($zipcodes[1])
                            ]
                        ]
                    ] 
                ];
            }
            
        } else {
            
            $answer = [
                'status' => 'error',
                'message' => 'The service requires 2 zip codes'
            ];
            
        }
        
        return $answer;

    }
    
    /*
     * match agents vs clients based on their zip codes
    */
    function _matchClosest($contacts, $zipCodeA, $zipCodeB) {
        
        $n = sizeof($contacts);
        $APoints = [];
        $BPoints = [];

        // Step 1: Assign a trend index. The most this trend index is negative, the most
        //   the contact should be assigned to A. Otherwise B.
        foreach($contacts as $contact) {
            $toA = $contact->distanceTo( $zipCodeA );
            $toB = $contact->distanceTo( $zipCodeB );
            
            if($toA == -1 || $toB == -1) {
                return [
                    'status' => 'error',
                    'message' => 'zip code not found'
                ];
                // TODO: Throw error instead of returning value
            }
            
            $contact->_trend = $toA - $toB;
        
        }
        
        

        // Step 2: Sort the points according to their trend
        usort($contacts, function($m, $n) use ($zipCodeA, $zipCodeB)
        {
            if($m->_trend == $n->_trend) {
                return ($this->_hasTheLongestDistance($m, $n, $zipCodeA, $zipCodeB)==$m)?-1:1;
            } else {
                return ($m->_trend - $n->_trend);
            }
        });

        // Step 3: Assign the points to A or B according
        // to their location inside the array of contacts
        $middle = floor($n/2);
        if($n % 2 == 0) {
            $APoints = array_slice($contacts, 0, $middle);
            $BPoints = array_slice($contacts, $middle);
        } else {
            if(n > 1) {
                $APoints = array_slice($contacts, 0, $middle);
                $BPoints = array_slice($contacts, $middle);
            }

            // If the number of contacts is odd, the contact in the
            //   middle is assigned to its closest agent
            $contact = $contacts[$middle];
            if($contact->_trend < 0) {
                $APoints[] = $contact;
            } else {
                $BPoints[] = $contact;
            }
        }

        return [
          'A' => $APoints,
          'B' => $BPoints
        ];
        
    }

    /*
     * Calculates which agent has the longest path to an agent
    */    
    function _hasTheLongestDistance($contactA, $contactB, $agentA_zipcode, $agentB_zipcode) {

        $maxDistance;
        $_obj = $contactA;
        $maxDistance = max(
            $contactA->distanceTo( $agentA_zipcode ),
            $contactA->distanceTo( $agentB_zipcode )
        );

        if($maxDistance < $contactB->distanceTo($agentA_zipcode)) {
            $_obj = $contactB;
            $maxDistance = $contactB->distanceTo($agentA_zipcode);
        }
        if($maxDistance < $contactB->distanceTo($agentB_zipcode)) {
            $_obj = $contactB;
            $maxDistance = $contactB->distanceTo($agentB_zipcode);
        }

        return $_obj;

    }
    
}
