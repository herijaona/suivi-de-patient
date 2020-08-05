<?php

namespace App\Repository;

use App\Entity\CarnetVaccination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CarnetVaccination|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarnetVaccination|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarnetVaccination[]    findAll()
 * @method CarnetVaccination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarnetVaccinationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarnetVaccination::class);
    }



    // /**
    //  * @return CarnetVaccination[] Returns an array of CarnetVaccination objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CarnetVaccination
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
