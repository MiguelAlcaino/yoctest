<?php

namespace App\Repository;

use App\Entity\WeatherRecordDaily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WeatherRecordDaily|null find($id, $lockMode = null, $lockVersion = null)
 * @method WeatherRecordDaily|null findOneBy(array $criteria, array $orderBy = null)
 * @method WeatherRecordDaily[]    findAll()
 * @method WeatherRecordDaily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeatherRecordDailyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WeatherRecordDaily::class);
    }

    public function getWeatherRecords(
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
        ?float $maxTemp = null,
        ?float $minTemp = null,
        ?int $limit = 10,
        ?int $offset = 10,
        ?bool $returnArray = false
    ){
        $rawSql = "SELECT
                      w.datetime,
                      c.country_code,
                      c.name,
                      w.temp
                   FROM city c
                   LEFT JOIN weather_record_daily w ON w.city_id = c.id ";

        if(!is_null($startDate) && !is_null($endDate)){
            $rawSql .= " WHERE w.datetime >= :start_date AND w.datetime <= :end_date ";
            $params = [
                'start_date' => $startDate->format('Y-m-d h:i:s'),
                'end_date' => $endDate->format('Y-m-d h:i:s')
            ];
        }else if(!is_null($startDate) && is_null($endDate)){
            $rawSql .= " WHERE w.datetime >= :start_date ";
            $params = [
                'start_date' => $startDate->format('Y-m-d h:i:s')
            ];
        }else if(is_null($startDate) && !is_null($endDate)){
            $rawSql .= " WHERE w.datetime <= :end_date ";
            $params = [
                'end_date' => $endDate->format('Y-m-d h:i:s')
            ];
        }else{
            $sevenDaysAgo = (new \DateTime('today'))->modify('-7 days');
            $rawSql .= " WHERE w.datetime >= :from_date";
            $params = [
                'from_date' => $sevenDaysAgo->format('Y-m-d h:i:s')
            ];
        }

        if(!is_null($maxTemp) && !is_null($minTemp)){
            $rawSql .= " AND w.temp > :min_temp AND w.temp < :max_temp ";
            $params['min_temp'] = $minTemp;
            $params['max_temp'] = $maxTemp;
        }else if(!is_null($maxTemp) && is_null($minTemp)){
            $rawSql .= " AND w.temp < :max_temp ";
            $params['max_temp'] = $maxTemp;
        }else if(is_null($maxTemp) && !is_null($minTemp)){
            $rawSql .= " AND w.temp > :min_temp ";
            $params['min_temp'] = $minTemp;
        }

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
