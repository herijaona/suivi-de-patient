<?php

namespace App\Repository;

use App\Entity\CentreType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CentreType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CentreType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CentreType[]    findAll()
 * @method CentreType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CentreTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CentreType::class);
    }

    // /**
    //  * @return CentreTypeFixture[] Returns an array of CentreTypeFixture objects
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
    public function findOneBySomeField($value): ?CentreTypeFixture
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
