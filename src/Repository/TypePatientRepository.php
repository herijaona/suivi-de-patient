<?php

namespace App\Repository;

use App\Entity\TypePatient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypePatient|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypePatient|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypePatient[]    findAll()
 * @method TypePatient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypePatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypePatient::class);
    }


    // /**
    //  * @return TypePatient[] Returns an array of TypePatient objects
    //  */
    /*
     *
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypePatient
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
