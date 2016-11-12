<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\Repositories\Air;
use \App\Repositories\ZipcodeRepository;

class Contact extends Model
{
    function distanceTo($toZipCode) {
        
        return ZipcodeRepository::calculateDistanceBetweenZipCodes($this->zipcode, $toZipCode);
        
    }
}
