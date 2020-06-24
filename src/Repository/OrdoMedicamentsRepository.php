<?php

namespace App\Repository;

use App\Entity\OrdoMedicaments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrdoMedicaments|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdoMedicaments|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdoMedicaments[]    findAll()
 * @method OrdoMedicaments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdoMedicamentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdoMedicaments::class);
    }

    // /**
    //  * @return OrdoMedicaments[] Returns an array of OrdoMedicaments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrdoMedicaments
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
