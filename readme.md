# Closest contacts by zipcode


## Running the project

Once the project has been cloned into a local folder, it can be easily run be executing PHP built-in server
php -S localhost:8080

For the seak of simplicity the project has been configured to use a SQlite as database


## Migrations and seeds

This project includes Laravel migrations for its 3 tables:

* contacts
* zipcodes
* agents (created but not used)

The project has a seeder for the contacts table, this seeder takes the information from the file "database/seeds/contacts.csv"

## Custom commands

### zipcode:cache

It was included a custom command for feeding the zipcodes table, in order to execute this command call:

php artisan zipcode:cache

Steps: 
1. Truncate zipcodes table
2. Get a list of the zip codes used by the contacts
3. Call an external API in order to convert the zip codes to latitude-longitude (API provider: https://www.zipcodeapi.com/) 
4. Create zipcodes records with the following information: [zip code, latitude, longitude]

http://www.zipcodeapi.com/ is used instead of Google Maps because it allows multiple zip code conversion

### zipcode:update

TODO: This command is not available yet
It is aimed to add missing zip codes instead of truncating the table

## Environment variables

ZIP_CODE_API_KEY: This is the key for https://www.zipcodeapi.com/ API


## Arquitecture

### Back-end

#### API

#####GET /api/contact/match-closer/{zip code 1},{zip code 2}

This request groups the contact in two group according to its distance to the zip codes provided. It balances the amount of contacts given to each agent.





