<?php

namespace App\Repository;

use App\Entity\PropositionRdv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PropositionRdv|null find($id, $lockMode = null, $lockVersion = null)
 * @method PropositionRdv|null findOneBy(array $criteria, array $orderBy = null)
 * @method PropositionRdv[]    findAll()
 * @method PropositionRdv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropositionRdvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropositionRdv::class);


    }
    public function searchStatusPatientNotif($patient = null, $status = 0,$statusNotif = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT count(p.id) as count
            FROM App\Entity\PropositionRdv p
            LEFT JOIN App\Entity\Praticien pr with pr.id = p.praticien
            LEFT JOIN App\Entity\Patient pa with pa.id = p.patient
            WHERE(pa.id = :patient OR pa.id IS NULL) AND p.dateProposition >= :now  AND p.statusProposition = :status AND p.statusNotif =:etat
            ORDER BY p.dateProposition ASC')
            ->setParameter('status', $status)
            ->setParameter('etat', $statusNotif)
            ->setParameter('patient', $patient)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }

    public function searchPropositio($patient = null, $status = 0, $statusNotif = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id, p.dateProposition, p.descriptionProposition, pr.firstName, pr.lastName, pr.id as praticien
            FROM App\Entity\PropositionRdv p
            LEFT JOIN App\Entity\Praticien pr with pr.id = p.praticien
            LEFT JOIN App\Entity\Patient pa with pa.id = p.patient
            WHERE(pa.id = :patient OR pa.id IS NULL) AND p.dateProposition >= :now  AND p.statusProposition = :status AND p.statusNotif =:etat
            ORDER BY p.dateProposition ASC')
            ->setParameter('status', $status)
            ->setParameter('patient', $patient)
            ->setParameter('etat', $statusNotif)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }

    public function searchProposition($patient= null, $status = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id, p.dateProposition, p.descriptionProposition, pr.firstName, pr.lastName, pr.id as praticien, pa.firstName as patientfirst, pa.lastName as patientlast, pa.id as patient 
            FROM App\Entity\PropositionRdv p
            LEFT JOIN App\Entity\Praticien pr with pr.id = p.praticien
            LEFT JOIN App\Entity\Patient pa with pa.id = p.patient
            WHERE(pa.id = :patient OR pa.id IS NULL) AND p.dateProposition >= :now  AND p.statusProposition = :status
            ORDER BY p.dateProposition ASC')
            ->setParameter('now', new \DateTime())
            ->setParameter('patient', $patient)
            ->setParameter('status', $status);

        return $query->getResult();
    }
    public function searchStatusPraticienEnValid($praticien = null, $status = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id, p.dateProposition, p.descriptionProposition, pa.lastName, pa.firstName
            FROM App\Entity\PropositionRdv p
            LEFT JOIN App\Entity\Praticien pr with pr.id = p.praticien
            LEFT JOIN App\Entity\Patient pa with pa.id = p.patient
            WHERE (pr.id = :praticien OR pr.id IS NULL) AND p.statusProposition = :status AND p.dateProposition >= :now 
            ORDER BY p.dateProposition ASC')
            ->setParameter('status', $status)
            ->setParameter('praticien', $praticien)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }

    // /**
    //  * @return PropositionRdv[] Returns an array of PropositionRdv objects
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
    public function findOneBySomeField($value): ?PropositionRdv
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
