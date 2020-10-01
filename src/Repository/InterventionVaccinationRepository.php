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

    public function  searchinterventionPatient($patient = null, $status = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT i.id, i.datePriseVaccin,i.etat,i.statusVaccin,p.firstName , p.lastName ,v.vaccinName
            FROM App\Entity\InterventionVaccination i 
            INNER JOIN App\Entity\Patient p with p.id = i.patient
            LEFT JOIN App\Entity\Vaccin v with v.id = i.vaccin
            WHERE p.id = :patient   AND i.datePriseVaccin >= :now AND i.statusVaccin = :status 
            ORDER BY i.datePriseVaccin ASC')
            ->setParameter('patient', $patient)
            ->setParameter('status', $status)
            ->setParameter('now', new \DateTime());

        return $query->getResult();


    }
    public function searchIntervationPraticien($praticien = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT i.id, i.datePriseVaccin,i.etat,i.statusVaccin,p.firstName , p.lastName ,v.vaccinName
            FROM App\Entity\InterventionVaccination i 
            INNER JOIN App\Entity\Patient p with p.id = i.patient
            LEFT JOIN App\Entity\Praticien pr with pr.id = i.praticienPrescripteur
            LEFT JOIN App\Entity\Vaccin v with v.id = i.vaccin
            WHERE pr.id = :praticien   
            ORDER BY i.datePriseVaccin ASC')
            ->setParameter('praticien', $praticien);

        return $query->getResult();
    }

    public function countUnrealizedVacc($praticien){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT COUNT(i.id)
            FROM App\Entity\InterventionVaccination i
            INNER JOIN App\Entity\Ordonnace o with o.id = i.ordonnace
            INNER JOIN App\Entity\Praticien p with p.id = o.praticien
            WHERE i.etat = :etat AND p.id = :praticien
        ')->setParameter('etat', 0)
          ->setParameter('praticien', $praticien);
        return $query->getResult();
    }

    public function countRealizedVacc($praticien){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT COUNT(i.id)
            FROM App\Entity\InterventionVaccination i
            INNER JOIN App\Entity\Ordonnace o with o.id = i.ordonnace
            INNER JOIN App\Entity\Praticien p with p.id = o.praticien
            WHERE i.etat = :etat AND p.id = :praticien
        ')->setParameter('etat', 1)
          ->setParameter('praticien', $praticien);
        return $query->getResult();
    }

    public function searchPatient($praticien = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT count(p.id)
            FROM App\Entity\InterventionVaccination i 
            INNER JOIN App\Entity\Patient p with p.id = i.patient
            LEFT JOIN App\Entity\Praticien pr with pr.id = i.praticienPrescripteur
            WHERE pr.id = :praticien  
            ORDER BY i.datePriseVaccin ASC')

            ->setParameter('praticien', $praticien);

        return $query->getResult();
    }
    public function  searchintervention(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT i.id, i.datePriseVaccin,i.etat,i.statusVaccin,p.firstName , p.lastName ,v.vaccinName, pr.lastName as praticienlast, pr.firstName as praticienfirst
            FROM App\Entity\InterventionVaccination i 
            INNER JOIN App\Entity\Praticien pr with pr.id = i.praticienPrescripteur
            INNER JOIN App\Entity\Patient p with p.id = i.patient
            LEFT JOIN App\Entity\Vaccin v with v.id = i.vaccin
            ORDER BY i.datePriseVaccin ASC');


        return $query->getResult();


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
