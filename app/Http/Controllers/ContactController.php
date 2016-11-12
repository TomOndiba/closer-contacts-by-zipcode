<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Contact;

class ContactController extends Controller
{
    
    function getByCloserZipCode($zipcodes)
    {
        
        $zipcodes = explode(',', $zipcodes);
        $answer = [];
        
        if( count($zipcodes) == 2 ) {
        
            $contacts = Contact::all()->all();
            $match = $this->_matchCloser($contacts, $zipcodes[0], $zipcodes[1]);
            $clasifiedContacts = [];
            $agents = ['A', 'B'];
            foreach($agents as $agent) {
                foreach($match[$agent] as $contact) {
                    $clasifiedContacts[] = [
                        'agent' => [
                            'id' => $agent
                        ],
                        'contact' => $contact,
                    ];
                }    
            }
            
            $answer = [
                'status' => 'success',
                'data' => $clasifiedContacts
            ];
            
            
        } else {
            
            $answer = [
                'status' => 'error',
                'message' => 'The service requires 2 zip codes'
            ];
            
        }
        
        return $answer;

    }
    
    function _matchCloser($contacts, $zipCodeA, $zipCodeB) {
        
        $n = sizeof($contacts);
        $APoints = [];
        $BPoints = [];

        foreach($contacts as $contact) {
            $contact->_trend = $contact->distanceTo( $zipCodeA ) - $contact->distanceTo( $zipCodeB );
        }

        // Sort the points according to the treding
        usort($contacts, function($m, $n)
        {
            if($m->_trend == $n->_trend) {
                return ($this->_hasTheLongestDistance($m,$n)==$m)?1:-1;
            } else {
                return ($m->_trend - $n->_trend);
            }
        });

        // Assign the points to A or B according
        // to their location inside the array of points
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

    function _hasTheLongestDistance($m, $n) {

        $maxDistance;
        $_obj = $m;
        $maxDistance = max($m->x, $n->x);

        if($maxDistance < $n->x) {
            $_obj = $n;
            $maxDistance = $n->x;
        }
        if($maxDistance < $n->y) {
            $_obj = $n;
            $maxDistance = $n->y;
        }

        return $_obj;

    }
    
}
