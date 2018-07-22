<?php

namespace App\Controller;

use App\Entity\City;
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
}
