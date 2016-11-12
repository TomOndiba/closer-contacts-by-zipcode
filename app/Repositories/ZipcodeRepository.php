<?php

namespace App\Repositories;

use \App\Repositories\Air;

/*
 * Zip code math
*/
class ZipcodeRepository {
 
    protected function __construct()
    {
    }
    
    private function __clone()
    {
    }
    
    private function __wakeup()
    {
    }
    
    /*
     * Calculate the distance between two zip codes
    */
    static function calculateDistanceBetweenZipCodes($zipCodeA, $zipCodeB) {
        
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
                ZipcodeRepository::zipcodeToLatLng($zipCodeA);
            $zipcodes['B'] = ISSET($cachedZipCodes[$zipCodeB])?
                $cachedZipCodes[$zipCodeB]:
                ZipcodeRepository::zipcodeToLatLng($zipCodeB);
            
            if( $zipcodes['A'] != null && $zipcodes['B'] != null ) {
                
                // Finally calculate the distance
                $lat1 = $zipcodes['A']->latitude;
                $lon1 = $zipcodes['A']->longitude;
                $lat2 = $zipcodes['B']->latitude;
                $lon2 = $zipcodes['B']->longitude;

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
    
    /*
     * Convert a zip code to latitude longitude
    */
    static function zipcodeToLatLng($zipcode) {
        
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
                $output->latitude = $output->lat;
                $output->longitude = $output->lng;

            } catch(\Exception $error) {
                //$serviceResponse = null;
                // TODO: Throw error instead of returning value
            }

            return $output;                
            
        });
        
    }
    
    
}