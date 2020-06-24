<?php

namespace App\Repository;

use App\Entity\VaccinCentreHealth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VaccinCentreHealth|null find($id, $lockMode = null, $lockVersion = null)
 * @method VaccinCentreHealth|null findOneBy(array $criteria, array $orderBy = null)
 * @method VaccinCentreHealth[]    findAll()
 * @method VaccinCentreHealth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VaccinCentreHealthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VaccinCentreHealth::class);
    }

    // /**
    //  * @return VaccinCentreHealth[] Returns an array of VaccinCentreHealth objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VaccinCentreHealth
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
