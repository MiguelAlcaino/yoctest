<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * Returns a list of all the countries
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getAllCountries(?int $limit = 10, ?int $offset = 0){
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('partial c.{id,countryCode, name}')
            ->from(Country::class,'c')
            ->orderBy('c.name')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        return $query->getArrayResult();
    }
}
