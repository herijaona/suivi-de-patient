<?php

namespace App\Repository;

use App\Entity\PatientIntervationConsultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientIntervationConsultation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientIntervationConsultation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientIntervationConsultation[]    findAll()
 * @method PatientIntervationConsultation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientIntervationConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientIntervationConsultation::class);
    }

    // /**
    //  * @return PatientIntervationConsultation[] Returns an array of PatientIntervationConsultation objects
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
    public function findOneBySomeField($value): ?PatientIntervationConsultation
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
