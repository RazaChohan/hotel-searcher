## Hotel Seacher
Hotel searcher allows you to search hotels from online resources. User can filter and search records using different parameters. 

[![Build Status](https://travis-ci.org/RazaChohan/hotel-searcher.png?branch=master)](https://travis-ci.org/RazaChohan/hotel-searcher)
[![Maintainability](https://api.codeclimate.com/v1/badges/cab07be86960ebe21e3d/maintainability)](https://codeclimate.com/github/RazaChohan/hotel-searcher/maintainability)      <a href="https://codeclimate.com/github/RazaChohan/hotel-searcher/test_coverage"><img src="https://api.codeclimate.com/v1/badges/cab07be86960ebe21e3d/test_coverage" /></a>

# Table of Contents
* [Configuration](#configuration)
* [Dependencies](#dependencies)
* [Technologies](#technologies-and-tools)
* [Setup](#setup)
* [Usage](#usage)
* [Run Tests](#run-tests)

## Configuration

Configuration of the application is stored in env.php Auth token is a random string that is required in hotel search
call. Authenication Middleware looks for the exact match of this auth token.

```
'HOTEL_API'  => 'https://api.myjson.com/bins/tl0bp',
'AUTH_TOKEN' => 'axKtsdvOpVLKe8yndbyv2DELsKFd1t3yrwbsliR3'
```
## Dependencies

```
"altorouter/altorouter": "^1.2"
"phpunit/phpunit": "^7.3"
"codeclimate/php-test-reporter": "^0.3.0@dev"

```

## Technologies and Tools
* PHP (v7.2)
* PHPunit
* Travis CI
* Code Climate

## Setup

Open terminal or command prompt and execute the following command

```
composer install
```

Run PHP built in server

```
php -S localhost:8000

``` 


## Run Tests

Tests are written using `PHPUnit` testing framework.

To run tests use following command.

```
./vendor/phpunit/phpunit/phpunit ./tests --coverage-text
```


## Usage

### Sample Call:
```
http://localhost:8000/hotel/search?auth_token=axKtsdvOpVLKe8yndbyv2DELsKFd1t3yrwbsliR3&date_range=01-1-2000:10-10-2050&destination=dubai
```

```
GET /hotels/search
```

### Params:
 
 * `name`       : Hotel name is matched partially
 * `destination`: City name is matched exactly
 * `price_range`: $100:$200 or $100 for exact match
 * `date_range` : 10-12-2020:15-12-2020 or 10-12:2020 for date between available dates
 * `sort_by`    : Field name to sort against
 * `sort_order` : Sorting order asc/desc
 * `auth_token` : Hard coded auth token (axKtsdvOpVLKe8yndbyv2DELsKFd1t3yrwbsliR3) 


### Response: 

 ```
{  
   message:"1 hotels found as per your search criteria",
   status:true,
   data:[  
      {  
         name:"Media One Hotel",
         destination:"dubai",
         price:102.2,
         availability:[  
            {  
               from:"10-10-2020",
               to:"15-10-2020",

            },
            {  
               from:"25-10-2020",
               to:"15-11-2020",

            },
            {  
               from:"10-12-2020",
               to:"15-12-2020",

            },

         ],

      }
   ],

}
 ```
 
## Search
In order to optimize the search the structure of data returned from the API is changed a bit. So bascially on hotels data in
a specifc city is stored in an associative array to separate the hotels from searching that are not in city required by the user. 


