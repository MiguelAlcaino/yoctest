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

    /**
     * Returns a list of weather records filtered by optional parameters
     * @param \DateTimeInterface|null $startDate
     * @param \DateTimeInterface|null $endDate
     * @param float|null $maxTemp
     * @param float|null $minTemp
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getWeatherRecords(
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
        ?float $maxTemp = null,
        ?float $minTemp = null,
        ?int $limit = 10,
        ?int $offset = 0
    ){
        $params = [];

        $rawSql = $this->getWeatherRecordsRawSql();
        $this->addStartAndEndDateConditionsToRawQuery($startDate, $endDate, $rawSql, $params);
        $this->addMaxAndMinTempConditionsToRawQuery($maxTemp, $minTemp, $rawSql, $params);

        $rawSql .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Returns the count of weather results
     * @param \DateTimeInterface|null $startDate
     * @param \DateTimeInterface|null $endDate
     * @param float|null $maxTemp
     * @param float|null $minTemp
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countWeatherRecords(
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
        ?float $maxTemp = null,
        ?float $minTemp = null
    ){
        $params = [];

        $rawSql = $this->getWeatherRecordsRawSql(true);
        $this->addStartAndEndDateConditionsToRawQuery($startDate, $endDate, $rawSql, $params);
        $this->addMaxAndMinTempConditionsToRawQuery($maxTemp, $minTemp, $rawSql, $params);

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /**
     * Returns a list of cities with their average temperatures
     * @param \DateTimeInterface|null $startDate
     * @param \DateTimeInterface|null $endDate
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAverageTempByCity(
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
        ?int $limit = 10,
        ?int $offset = 0
    ){
        $params = [];
        $rawSql = $this->getAverageWeatherRawSql();
        $this->addStartAndEndDateConditionsToRawQuery($startDate, $endDate, $rawSql, $params);

        $rawSql .= " GROUP BY c.id ";
        $rawSql .= " LIMIT :limit OFFSET :offset";

        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Returns the count of cities and their weather reports
     * @param \DateTimeInterface|null $startDate
     * @param \DateTimeInterface|null $endDate
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countAverageTempByCity(
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null
    ){
        $params = [];
        $rawSql = $this->getAverageWeatherRawSql(true);
        $this->addStartAndEndDateConditionsToRawQuery($startDate, $endDate, $rawSql, $params);

        $rawSql .= " GROUP BY c.id ";

        $rawSql = "SELECT COUNT(1) FROM (".$rawSql.") temp";
        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /**
     * Adds the start and end dates conditions to a raw sql query. $rawSql and $params are passed by reference
     * @param \DateTimeInterface|null $startDate
     * @param \DateTimeInterface|null $endDate
     * @param string $rawSql
     * @param array $params
     */
    private function addStartAndEndDateConditionsToRawQuery(
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
        string &$rawSql,
        array &$params
    ){
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
    }

    /**
     * Adds the max and min temp conditions to a raw sql query. $rawSql and $params are passed by reference
     * @param float|null $maxTemp
     * @param float|null $minTemp
     * @param string $rawSql
     * @param array $params
     */
    private function addMaxAndMinTempConditionsToRawQuery(?float $maxTemp, ?float $minTemp, string &$rawSql, array &$params){
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
    }

    /**
     * Returns the weather records sql query
     * @param bool $count - Whether or not is the query to count results
     * @return string
     */
    private function getWeatherRecordsRawSql(?bool $count = false){
        if($count){
            $rawSql = "SELECT COUNT(w.id) ";
        }else{
            $rawSql = "SELECT
                      w.datetime,
                      country.country_code,
                      country.name as country_name,
                      c.name as city_name,
                      w.temp ";
        }
        $rawSql .= " FROM city c
                   LEFT JOIN country country ON country.id = c.country_id
                   LEFT JOIN weather_record_daily w ON w.city_id = c.id ";
        return $rawSql;
    }

    private function getAverageWeatherRawSql(?bool $count = false){
        if($count){
            $rawSql = "SELECT COUNT(c.id) ";
        }else{
            $rawSql = "SELECT
                      country.country_code,
                      country.name as country_name,
                      c.name as city_name,
                      ROUND(AVG(w.temp),2) as avg_temp ";
        }
        $rawSql .= "FROM city c
                    LEFT JOIN country country ON country.id = c.country_id
                    LEFT JOIN weather_record_daily w ON w.city_id = c.id ";

        return $rawSql;
    }
}
