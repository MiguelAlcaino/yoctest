<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\WeatherRecordDaily;
use App\Services\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApiController extends Controller
{
    const MAX_RESULTS_PER_PAGE = 10;

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
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function weatherAction(Request $request){
        $manager = $this->getDoctrine()->getManager();
        $page = $request->query->get('page',1);
        $offset = ($page - 1)*self::MAX_RESULTS_PER_PAGE;
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

        $weatherRecords = $manager->getRepository(WeatherRecordDaily::class)->getWeatherRecords(
            $startDate,
            $endDate,
            $maxTemp,
            $minTemp,
            self::MAX_RESULTS_PER_PAGE,
            $offset
        );

        $count = $manager->getRepository(WeatherRecordDaily::class)->countWeatherRecords(
            $startDate,
            $endDate,
            $maxTemp,
            $minTemp
        );

        $responseArray = [
            'data' => $weatherRecords
        ];

        $paginator = new Paginator($count, self::MAX_RESULTS_PER_PAGE, $page);
        $this->addPaginationToArrayResponse($request, $paginator, $responseArray);

        return new JsonResponse($responseArray);
    }

    /**
     * @Route("/avg-temp", name="api_average_temp", methods={"GET"})
     */
    public function avgTempCityAction(Request $request){
        $manager = $this->getDoctrine()->getManager();
        $page = $request->query->get('page',1);
        $offset = ($page - 1)*self::MAX_RESULTS_PER_PAGE;

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
            $endDate,
            self::MAX_RESULTS_PER_PAGE,
            $offset
        );

        $count = $manager->getRepository(WeatherRecordDaily::class)->countAverageTempByCity(
            $startDate,
            $endDate
        );

        $responseArray = [
            'data' => $avgByCityResult
        ];

        $paginator = new Paginator($count, self::MAX_RESULTS_PER_PAGE, $page);
        $this->addPaginationToArrayResponse($request, $paginator, $responseArray);

        return new JsonResponse($responseArray);
    }

    /**
     * @Route("/countries", name="api_country_list", methods={"GET"})
     */
    public function getListOfCountries(Request $request){
        $manager = $this->getDoctrine()->getManager();
        $page = $request->query->get('page',1);
        $offset = ($page - 1)*self::MAX_RESULTS_PER_PAGE;

        $countries = $manager->getRepository(Country::class)->getAllCountries(
            self::MAX_RESULTS_PER_PAGE,
            $offset
        );

        $count = $manager->getRepository(Country::class)->countCountries();

        $responseArray = [
            'data' => $countries
        ];

        $paginator = new Paginator($count, self::MAX_RESULTS_PER_PAGE, $page);
        $this->addPaginationToArrayResponse($request, $paginator, $responseArray);

        return new JsonResponse($responseArray);
    }

    /**
     * @Route("/cities", name="api_cities_list", methods={"GET"})
     */
    public function getAllCities(Request $request){
        $manager = $this->getDoctrine()->getManager();

        $page = $request->query->get('page',1);
        $offset = ($page - 1)*self::MAX_RESULTS_PER_PAGE;
        $countryCode = $request->query->get('country_code');

        $cities = $manager->getRepository(City::class)->getCities(
            $countryCode,
            self::MAX_RESULTS_PER_PAGE,
            $offset
        );

        $count = $manager->getRepository(City::class)->countCities(
            $countryCode
        );

        $responseArray = [
            'data' => $cities
        ];

        $paginator = new Paginator($count, self::MAX_RESULTS_PER_PAGE, $page);
        $this->addPaginationToArrayResponse($request, $paginator, $responseArray);

        return new JsonResponse($responseArray);
    }

    /**
     * Adds pagination keys to a response array passed by reference
     * @param Request $request
     * @param Paginator $paginator
     * @param array $responseArray
     */
    private function addPaginationToArrayResponse(Request $request, Paginator $paginator, array &$responseArray){

        $routeParams = [];

        foreach ($request->query->all() as $key => $value) {
            if($key !== 'page'){
                $routeParams[$key] = $value;
            }
        }

        if($paginator->hasNextPage()){
            $routeParams['page'] = $paginator->getCurrentPage()+1;
            $responseArray['pagination']['next'] = $this->generateUrl($request->get('_route'), $routeParams, UrlGeneratorInterface::ABSOLUTE_URL);
        }
        if($paginator->hasPreviousPage()){
            $routeParams['page'] = $paginator->getCurrentPage()-1;
            $responseArray['pagination']['previous'] = $this->generateUrl($request->get('_route'), $routeParams, UrlGeneratorInterface::ABSOLUTE_URL);
        }
    }

    /**
     * @param Request $request
     * @Route("/best-weekend", name="api_best_weekend", methods={"GET"})
     */
    public function bestWeekendAction(Request $request){
        $manager = $this->getDoctrine()->getManager();
        $page = $request->query->get('page',1);
        $offset = ($page - 1)*self::MAX_RESULTS_PER_PAGE;
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

        $weatherRecords = $manager->getRepository(WeatherRecordDaily::class)->getMaxTempWeekendByCity(
            $startDate,
            $endDate
        );

        $responseArray = [
            'data' => $weatherRecords
        ];

        return new JsonResponse($responseArray);
    }
}
