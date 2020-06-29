<?php

namespace App\Repository;

use App\Entity\InterventionVaccination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InterventionVaccination|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterventionVaccination|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterventionVaccination[]    findAll()
 * @method InterventionVaccination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterventionVaccinationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterventionVaccination::class);
    }

    // /**
    //  * @return InterventionVaccination[] Returns an array of InterventionVaccination objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InterventionVaccination
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
