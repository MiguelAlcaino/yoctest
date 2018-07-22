<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, City::class);
    }

    /**
     * Returns a list of cities with their countries
     * @param null|string $countryCode
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getCities(?string $countryCode = null, ?int $limit = 10, ?int $offset = 0){
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('partial c.{id,name}, partial country.{id}')
            ->from(City::class,'c')
            ->leftJoin('c.country','country')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if(!is_null($countryCode)){
            $qb->where('country.countryCode = :country_code')
                ->setParameter('country_code', $countryCode);
        }

        $query = $qb->getQuery();

        return $query->getArrayResult();
    }
}
