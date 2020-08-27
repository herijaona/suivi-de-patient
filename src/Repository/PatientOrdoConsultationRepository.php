<?php

namespace App\Repository;

use App\Entity\PatientOrdoConsultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PatientOrdoConsultation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientOrdoConsultation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientOrdoConsultation[]    findAll()
 * @method PatientOrdoConsultation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientOrdoConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientOrdoConsultation::class);
    }
    public function searchStatus($patient = null, $status = 0 ){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT 
            FROM App\Entity\PatientOrdoConsultation p 
            INNER JOIN App\Entity\Patient p with p.id = o.patient
            LEFT JOIN App\Entity\Ordonnace d with d.id = o.ordonnance
            LEFT JOIN App\Entity\Praticien pr with pr.id = d.praticien
            WHERE p.id = :patient AND o.statusConsultation = :status AND o.dateRdv >= :now 
            ORDER BY o.dateRdv ASC')
            ->setParameter('status', $status)
            ->setParameter('patient', $patient)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }


    // /**
    //  * @return PatientOrdoConsultation[] Returns an array of PatientOrdoConsultation objects
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
    public function findOneBySomeField($value): ?PatientOrdoConsultation
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
