<?php

namespace App\Repository;

use App\Entity\VaccinPraticien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VaccinPraticien|null find($id, $lockMode = null, $lockVersion = null)
 * @method VaccinPraticien|null findOneBy(array $criteria, array $orderBy = null)
 * @method VaccinPraticien[]    findAll()
 * @method VaccinPraticien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VaccinPraticienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VaccinPraticien::class);
    }

    // /**
    //  * @return VaccinPraticien[] Returns an array of VaccinPraticien objects
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
    public function findOneBySomeField($value): ?VaccinPraticien
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
