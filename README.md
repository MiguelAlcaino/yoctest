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
 - Run the api server executing `bin/console server:run`. The host will probably be `http://localhost:8000`.
 - All the endpoints count with a optional parameter `(int) page` if it's not present its default value is `1`
 - **Task 3.1**: Request `GET /` to see Hello World!
 - **Task 3.2**: Request `GET /weather` to get daily weather records. Example request: `GET /weather?end_date=2018-07-22&start_date=2018-06-01&max_temp=22.3&min_temp=17.6`
    - Optional Parameters:
      - `(string) start_date`: Day when you want the records start from.
      - `(string) end_date`: Day when you want the records to end.
      - `(float) max_temp`: Max temp for records.
      - `(float) min_temp`:  Min temp for records.
 - **Task 3.3**: Request `GET /avg-temp` to get the average temperature of the cities inside the system. Example request: `GET /avg-temp?end_date=2018-07-22&start_date=2018-06-01`
    - Optional Parameters
       - `(string) start_date`: Day when you want the records start from.
       - `(string) end_date`: Day when you want the records to end.
 - **Task 3.4**: Request `GET /countries` to get a list of the countries inside the system. Example request `GET /countries`
 - **Task 3.5**: Request `GET /cities` to get a list of the cities inside the system. Example request: `GET /cities?country_code=DE`
    - Optional Parameters
        - `(string) country_code`: Code of the country to filter the cities displayed

Run it with Docker
------
The app has been dockerized so it can run its own environment exposin a web nginx server using the port `8080`.
 -Requirements
   - Docker compose
 - Installation
   - Run `docker-compose up` and keep the terminal window open. You can add the option `-d` to run it in the background.
   - With your browser access to `http://localhost:8080`

Assumptions and workarounds made
----------------
 - As the command line tool says that it parameters are optionals, a constant `ALLOWED_COUNTRY_CITIES` defined in `src/Services/WeatherApiService.php` has been created in order to get all the cities' weather reports when no parameter is passed to the tool.
 - The country code for UK inside the api result is "GB". That's why inside The weather service there is a special attribute for it.
 - The country name is no coming inside the result of the api endpoints. So they are obtain from the constant `ALLOWED_COUNTRY_CITIES` defined in `src/Services/WeatherApiService.php` 