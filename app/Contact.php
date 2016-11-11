<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    
    static function _calculateDistanceBetweenZipCodes($zipCodeA, $zipCodeB) {
        
        return Math.sqrt(
            Math.pow(A.x-point.x,2)+
            Math.pow(A.y-point.y,2)
        );        
        
    }
    
    function distanceTo($toZipCode) {
        
        return Contact::_calculateDistanceBetweenZipCodes($this->zipcode, $toZipCode);
        
    }
}
