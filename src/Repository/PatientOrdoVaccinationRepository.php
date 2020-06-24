<?php

namespace App\Repository;

use App\Entity\PatientOrdoVaccination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientOrdoVaccination|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientOrdoVaccination|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientOrdoVaccination[]    findAll()
 * @method PatientOrdoVaccination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientOrdoVaccinationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientOrdoVaccination::class);
    }

    // /**
    //  * @return PatientOrdoVaccination[] Returns an array of PatientOrdoVaccination objects
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
    public function findOneBySomeField($value): ?PatientOrdoVaccination
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
