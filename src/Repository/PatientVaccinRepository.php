<?php

namespace App\Repository;

use App\Entity\PatientVaccin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientVaccin|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientVaccin|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientVaccin[]    findAll()
 * @method PatientVaccin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientVaccinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientVaccin::class);
    }

    // /**
    //  * @return PatientVaccin[] Returns an array of PatientVaccin objects
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
    public function findOneBySomeField($value): ?PatientVaccin
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
