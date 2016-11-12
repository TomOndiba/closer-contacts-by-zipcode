<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    
    static function _calculateDistanceBetweenZipCodes($zipCodeA, $zipCodeB) {
        
        $distance = 0;
        $zipcodes = Zipcode::whereIn('zipcode', [$zipCodeA, $zipCodeB])->take(2);
        if(count($zipcodes) == 2) {
            
            $lat1 = $zipcodes[0]->lat;
            $lon1 = $zipcodes[0]->lng;
            $lat2 = $zipcodes[1]->lat;
            $lon2 = $zipcodes[1]->lng;
            
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $distance = $miles;
            
        }
        return $distance;
        
    }
    
    function distanceTo($toZipCode) {
        
        return Contact::_calculateDistanceBetweenZipCodes($this->zipcode, $toZipCode);
        
    }
}
