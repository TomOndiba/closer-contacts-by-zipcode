<?php

use Illuminate\Database\Seeder;
use \App\Contact;

class ContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $file_path = database_path() . '/seeds/contacts.csv';
        
        $csv = array_map('str_getcsv', file($file_path) );
        array_shift($csv);
        
        $contacts = array_map(function($data) {
            return [
                'name' => $data[0],
                'zipcode' => $data[1]
            ];
        }, $csv);
        
        Contact::insert($contacts);
        
    }
}
