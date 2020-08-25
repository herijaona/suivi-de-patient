<?php

namespace App\Repository;

use App\Entity\PatientOrdoMedicaments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientOrdoMedicaments|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientOrdoMedicaments|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientOrdoMedicaments[]    findAll()
 * @method PatientOrdoMedicaments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientOrdoMedicamentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientOrdoMedicaments::class);
    }

    // /**
    //  * @return PatientOrdoMedicaments[] Returns an array of PatientOrdoMedicaments objects
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
    public function findOneBySomeField($value): ?PatientOrdoMedicaments
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
