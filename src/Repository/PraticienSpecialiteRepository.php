<?php

namespace App\Repository;

use App\Entity\PraticienSpecialite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PraticienSpecialite|null find($id, $lockMode = null, $lockVersion = null)
 * @method PraticienSpecialite|null findOneBy(array $criteria, array $orderBy = null)
 * @method PraticienSpecialite[]    findAll()
 * @method PraticienSpecialite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PraticienSpecialiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PraticienSpecialite::class);
    }

    // /**
    //  * @return PraticienSpecialite[] Returns an array of PraticienSpecialite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PraticienSpecialite
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
