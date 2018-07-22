<?php
/**
 * Created by PhpStorm.
 * User: malcaino
 * Date: 20/07/18
 * Time: 16:09
 */

namespace App\Services;


use App\Exceptions\CityNotFoundException;
use App\Exceptions\CountryNotFoundException;
use GuzzleHttp\Client;

class WeatherApiService
{
    CONST API_URL = 'https://yoc-media.github.io/weather/report';
    CONST ALLOWED_COUNTRY_CITIES = [
        'DE' => [
            'name' => 'Germany',
            'cities' => [
                'Berlin',
                'Dusseldorf'
            ]
        ],
        'ES' => [
            'name' => 'Spain',
            'cities' => ['Madrid']
        ],
        'AT' => [
            'name' => 'Austria',
            'cities' => ['Vienna']
        ],
        'PL' => [
            'name' => 'Poland',
            'cities' => ['Warsaw']
        ],
        'NL' => [
            'name' => 'Netherlands',
            'cities' => ['Amsterdam']
        ],
        'GB' => [
            'name' => 'United Kingdom',
            'api_country_code' => 'UK',
            'cities' => ['London']
        ]
    ];

    /**
     * @param null|string $countryCode
     * @param null|string $cityName
     * @return array
     * @throws CityNotFoundException
     * @throws CountryNotFoundException
     */
    public function getCityWeatherReport(?string $countryCode = null, ?string $cityName = null){
        $result = $this->getValidCountryAndCity($countryCode, $cityName);
        $arrayResult = [];
        foreach($result['data'] as $countryCode => $country){
            foreach($country['cities'] as $city){
                if(array_key_exists('api_country_code', $country)){
                    $arrayResult[] = $this->callRequestWeather($country['api_country_code'], $city);
                }else{
                    $arrayResult[] = $this->callRequestWeather($countryCode, $city);
                }

            }
        }

        return $arrayResult;
    }

    /**
     * @param null|string $countryCode
     * @param null|string $cityName
     * @return array
     * @throws CityNotFoundException
     * @throws CountryNotFoundException
     */
    private function getValidCountryAndCity(?string $countryCode, ?string $cityName){

        if(is_null($countryCode) && is_null($cityName)){
            return [
                'type' => 'all',
                'data' => self::ALLOWED_COUNTRY_CITIES
            ];
        }// Case when countryCode is not null and does not belong to ALLOWED_COUNTRY_CITIES
        else if(!is_null($countryCode) && !array_key_exists($countryCode, self::ALLOWED_COUNTRY_CITIES)){
            throw new CountryNotFoundException('Country has not been found');
        }
        /* Case when cityName is not null. It will check the cityName inside ALLOWED_COUNTRY_CITIES as return if there
          is a match. If there is not a match a CityNotFoundException will be thrown */
        else if(!is_null($cityName)){
            foreach(self::ALLOWED_COUNTRY_CITIES as $countryCodeAux => $country){
                foreach($country['cities'] as $countryCity){
                    if($cityName === $countryCity){
                        return [
                            'type' => 'city',
                            'data' => [
                                $countryCodeAux => [
                                    'name' => self::ALLOWED_COUNTRY_CITIES[$countryCodeAux]['name'],
                                    'cities' => [$cityName]
                                ]
                            ]
                        ];
                    }
                }
            }
            throw new CityNotFoundException('City has not been found');
        }// Case when countryCode is found and cityName is null
        else if(array_key_exists($countryCode, self::ALLOWED_COUNTRY_CITIES) && is_null($cityName)){
            return [
                'type' => 'country',
                'data' => [
                    $countryCode => [
                        'name' => self::ALLOWED_COUNTRY_CITIES[$countryCode]['name'],
                        'cities' => self::ALLOWED_COUNTRY_CITIES[$countryCode]['cities']
                    ]
                ]
            ];
        }
    }

    private function callRequestWeather(string $countryCode, string $cityName){
        $client = new Client();
        $endpointUrl = self::API_URL.'/'.$countryCode.'/'.$cityName.'.json';
        $res = $client->request('GET', $endpointUrl);
        return \GuzzleHttp\json_decode((string) $res->getBody());
    }
}