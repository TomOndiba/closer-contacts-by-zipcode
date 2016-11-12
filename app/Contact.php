<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\Repositories\Air;

class Contact extends Model
{
    
    static function _calculateDistanceBetweenZipCodes($zipCodeA, $zipCodeB) {
        
        return Air::get($zipCodeA . '-' . $zipCodeB, function() use ($zipCodeA, $zipCodeB) {
            
            $distance = 0;
            
            // Access to zip codes in database is done only once,
            //   Thinking in huge amounts of data, a chunk implementation supported by REDIS can be designed
            $cachedZipCodes = Air::get('cached-zip-codes', function() {
                return Zipcode::all()->keyBy('zipcode')->all();
            });
            
            // Requesting Zip code for Agent A, if it doesn't exist in database, let's google it
            $zipcodes = [];
            $zipcodes['A'] = ISSET($cachedZipCodes[$zipCodeA])?
                $cachedZipCodes[$zipCodeA]:
                Contact::_zipcodeToLatLng($zipCodeA);
            $zipcodes['B'] = ISSET($cachedZipCodes[$zipCodeB])?
                $cachedZipCodes[$zipCodeB]:
                Contact::_zipcodeToLatLng($zipCodeB);
            
            if( $zipcodes['A'] != null && $zipcodes['B'] != null ) {
                
                // Finally calculate the distance
                $lat1 = $zipcodes['A']->lat;
                $lon1 = $zipcodes['A']->lng;
                $lat2 = $zipcodes['B']->lat;
                $lon2 = $zipcodes['B']->lng;

                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $distance = $miles;

                
                
            } else {
                
                // TODO: Throw error instead of returning value
                $distance = -1;
                
            }
            
            return $distance;                
            
            
            
        });    
        
    }
    
    static function _zipcodeToLatLng($zipcode) {
        
        return Air::get($zipcode.'toLatLng', function() use ($zipcode) {
            
            $output = null;
        
            // Using google as a provider, but it should be replaced for a local solution in production server
            //   The less the project relays on external providers, the better
            $google_uri = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . $zipcode;
            $serviceResponse = '';
            try {
                $serviceResponse = file_get_contents($google_uri);
                $serviceResponse = rtrim($serviceResponse, "\0");
                $serviceResponse = json_decode(trim($serviceResponse));
                $output = $serviceResponse->results[0]->geometry->location;

            } catch(\Exception $error) {
                //$serviceResponse = null;
                // TODO: Throw error instead of returning value
            }

            return $output;                
            
        });
        
    }
    
    function distanceTo($toZipCode) {
        
        return Contact::_calculateDistanceBetweenZipCodes($this->zipcode, $toZipCode);
        
    }
}
