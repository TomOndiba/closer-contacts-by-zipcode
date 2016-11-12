<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Zipcode;
use \App\Contact;
use \DB;

class CacheZipcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zipcode:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Given the contacts' zip codes, cache latitude and longitude";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        DB::table('zipcodes')->truncate();
        
        $ZIP_CODE_API_KEY = env('ZIP_CODE_API_KEY', 'mysql');
        $api = 'https://www.zipcodeapi.com/rest/'.$ZIP_CODE_API_KEY.'/multi-info.json/{codes}/degrees';
        
        $codes = Contact::pluck("zipcode")->unique()->implode(',');
        $locations = json_decode(file_get_contents(str_replace('{codes}', $codes, $api)));
        $zipcodes = [];
        
        foreach($locations as $zipcode=>$location) {
            $zipcodes[] = [
                'zipcode' => $zipcode,
                'latitude' => $location->lat,
                'longitude' => $location->lng,
            ];
        }
        
        Zipcode::insert($zipcodes);
    }
}

