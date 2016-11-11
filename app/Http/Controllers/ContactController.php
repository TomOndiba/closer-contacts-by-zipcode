<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Contact;

class ContactController extends Controller
{
    
    function getByCloserZipCode()
    {
        
        $contacts = Contact::all();
        $APoints = [];
        $BPoints = [];
        
        foreach($contacts->cursor() as $contact) {            
            $contact->trend = $contact->distanceTo(10000) - $distanceTo(20000);
        }
        
        // Sort the points according to the treding
        usort($contacts, function($m, $n)
        {
            if($m.trend == $n.trend) {
                return (hasTheLongestDistance($m,$n)==$m)?1:-1;
            } else {
                return ($m.trending - $n.trending);
            }
        });

        // Assign the points to A or B according
        // to their location inside the array of points
        $middle = floor($n/2);
        if($n % 2 == 0) {
            $APoints = points.slice(0,middle);
            $BPoints = points.slice(middle );
        } else {
            if(n > 1) {
                $APoints = array_slice($contacts, 0, $middle);
                $BPoints = array_slice($contacts, $middle);
            }
            
            // If the number of contacts is odd, the contact in the
            //   middle is assigned to its closest agent
            $contact = $contacts[$middle];
            if($contact->trend < 0) {
                $APoints[] = $contact;
            } else {
                $BPoints[] = $contact;
            }
        }

        $answer = [
          'A' => $APoints,
          'B' => $BPoints
        ];
        
        return $answer;

    }

    function hasTheLongestDistance($m, $n) {

        $maxDistance;
        $_obj = m;
        $maxDistance = max($m->x, $n->x);

        if($maxDistance < $n.x) {
            $_obj = $n;
            $maxDistance = $n.x;
        }
        if($maxDistance < $n.y) {
            $_obj = $n;
            $maxDistance = $n.y;
        }

        return $_obj;

    }
    
}
