YOC Test by Miguel Alcaino
==========================

Requirements
------------
 - PHP >= v7.1.3
 - php-sqlite module activated
 - Composer
 - Linux. (It should work on OSX as well, but it has been tested only in linux)
 
Installation
------------
 - Clone the project
 - Open a terminal and go inside the project folder
 - Run `composer install`
 - Run `bin/console doctrine:database:create`
 - Run `bin/console doctrine:schema:update --force`
 
Using the project
-----------------
 
Command line tool (TASK 2)
-----------------

There is a command line tool created to satisfy the task 2.

 - Command `bin/console yoc:weather:request`. Used with no options it will request the data related to all the cities defined in the task and persists that data inside the database. 
 - Options:
   - `--country-code`. Requests the data from the YOC weather api endpoint for that specific country. Example: `bin/console yoc:weather:request --country-code=DE`
   - `--city-name`. Request the data from the YOC weather api endpoint for a specific city. Example `bin/console yoc:weather:request --city-name=Berlin`
    
Own Weather API
---------------

 - Task 3.1: Request `GET /` to see Hello World!
 - Task 3.2: Request `GET /weather` to get daily weather records. Example request: `GET /weather?end_date=2018-07-22&start_date=2018-06-01&max_temp=22.3&min_temp=17.6`
    - Optional Parameters:
      - `(string) start_date`: Day when you want the records start from.
      - `(string) end_date`: Day when you want the records to end
      - `(float) max_temp`: Max temp for records
      - `(float) min_temp`:  Min temp for records
