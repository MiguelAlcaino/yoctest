<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\WeatherRecordDaily;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiController extends Controller
{
    /**
     * @Route("/", name="api_index",methods={"GET"})
     */
    public function index()
    {
        return new JsonResponse([
            'hello' => 'world!'
        ]);
    }

    /**
     * @Route("/weather", name="api_weather", methods={"GET"})
     */
    public function weatherAction(Request $request){
        $manager = $this->getDoctrine()->getManager();

        $startDate = $request->query->get('start_date');
        if(!is_null($startDate)){
            $startDate = new \DateTime($startDate);
            $startDate->setTime(0,0,0);
        }

        $endDate = $request->query->get('end_date');
        if(!is_null($endDate)){
            $endDate = new \DateTime($endDate);
            $endDate->setTime(0,0,0);
        }

        $maxTemp = $request->query->get('max_temp');
        $minTemp = $request->query->get('min_temp');

        $cities = $manager->getRepository(WeatherRecordDaily::class)->getWeatherRecords(
            $startDate,
            $endDate,
            $maxTemp,
            $minTemp
        );

        return new JsonResponse($cities);
    }

    /**
     * @Route("/avg-temp", name="api_average_temp", methods={"GET"})
     */
    public function avgTempCityAction(Request $request){
        $manager = $this->getDoctrine()->getManager();

        $startDate = $request->query->get('start_date');
        if(!is_null($startDate)){
            $startDate = new \DateTime($startDate);
            $startDate->setTime(0,0,0);
        }

        $endDate = $request->query->get('end_date');
        if(!is_null($endDate)){
            $endDate = new \DateTime($endDate);
            $endDate->setTime(0,0,0);
        }

        $avgByCityResult = $manager->getRepository(WeatherRecordDaily::class)->getAverageTempByCity(
            $startDate,
            $endDate
        );

        return new JsonResponse($avgByCityResult);
    }

    /**
     * @Route("/countries", name="api_country_list", methods={"GET"})
     */
    public function getListOfCountries(){
        $manager = $this->getDoctrine()->getManager();
        $countries = $manager->getRepository(Country::class)->getAllCountries();

        return new JsonResponse($countries);
    }

    /**
     * @Route("/cities", name="api_cities_list", methods={"GET"})
     */
    public function getAllCities(Request $request){
        $manager = $this->getDoctrine()->getManager();
        $countryCode = $request->query->get('country_code');

        $cities = $manager->getRepository(City::class)->getCities(
            $countryCode
        );

        return new JsonResponse($cities);
    }
}
