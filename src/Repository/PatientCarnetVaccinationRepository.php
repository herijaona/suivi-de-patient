<?php

namespace App\Repository;

use App\Entity\PatientCarnetVaccination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientCarnetVaccination|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientCarnetVaccination|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientCarnetVaccination[]    findAll()
 * @method PatientCarnetVaccination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientCarnetVaccinationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientCarnetVaccination::class);
    }

    // /**
    //  * @return PatientCarnetVaccination[] Returns an array of PatientCarnetVaccination objects
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
    public function findOneBySomeField($value): ?PatientCarnetVaccination
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
