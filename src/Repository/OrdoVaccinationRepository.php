<?php

namespace App\Repository;

use App\Entity\OrdoVaccination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrdoVaccination|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdoVaccination|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdoVaccination[]    findAll()
 * @method OrdoVaccination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdoVaccinationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdoVaccination::class);
    }

    public function searchStatus($patient = null, $status = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.id, o.datePrise,  o.statusVaccin, pr.firstName, pr.lastName,v.vaccinName FROM App\Entity\OrdoVaccination o 
        INNER JOIN App\Entity\Patient p with p.id= o.patient
        LEFT JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
        LEFT JOIN App\Entity\Praticien pr with pr.id=d.praticien
        INNER JOIN App\Entity\Vaccin v with v.id = o.vaccin
         WHERE p.id= :patient AND o.statusVaccin= :status AND o.datePrise >= :now
            ORDER BY o.datePrise ASC')
            ->setParameter('status', $status)
            ->setParameter('patient', $patient)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }

    public function searchStatusPraticien($praticien = null, $status = 0, $etat=0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.id, o.datePrise,o.etat,  o.statusVaccin, p.firstName, p.lastName,v.id as vaccin,v.vaccinName, pr.id as praticien, p.id as patient FROM App\Entity\OrdoVaccination o 
        INNER JOIN App\Entity\Patient p with p.id= o.patient
        LEFT JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
        LEFT JOIN App\Entity\Praticien pr with pr.id=d.praticien
        INNER JOIN App\Entity\Vaccin v with v.id = o.vaccin
         WHERE pr.id= :praticien AND o.statusVaccin= :status  AND o.etat= :etat AND o.datePrise >= :now
            ORDER BY o.datePrise ASC')
            ->setParameter('status', $status)
            ->setParameter('etat', $etat)
            ->setParameter('praticien', $praticien)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }

    public function searchStatusPraticienEnValid($praticien = null, $status = 0, $etat = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.id, o.datePrise,  o.statusVaccin, p.firstName, p.lastName,v.vaccinName FROM App\Entity\OrdoVaccination o 
        INNER JOIN App\Entity\Patient p with p.id= o.patient
        LEFT JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
        LEFT JOIN App\Entity\Praticien pr with pr.id=d.praticien
        INNER JOIN App\Entity\Vaccin v with v.id = o.vaccin
         WHERE (pr.id= :praticien OR pr.id IS NULL) AND o.statusVaccin= :status AND o.etat= :etat AND o.datePrise >= :now
            ORDER BY o.datePrise ASC')
            ->setParameter('etat', $etat)
            ->setParameter('status', $status)
            ->setParameter('praticien', $praticien)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }

    public function searchstatusvalider($patient= null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.datePrise,  o.statusVaccin, pr.firstName, pr.lastName,v.vaccinName FROM App\Entity\OrdoVaccination o 
        INNER JOIN App\Entity\Patient p with p.id= o.patient
        INNER JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
        INNER JOIN App\Entity\Praticien pr with pr.id=d.praticien
        INNER JOIN App\Entity\Vaccin v with v.id=o.vaccin
         WHERE p.id= :patient AND o.statusVaccin= :valider ')
            ->setParameter('valider', "Valider")
            ->setParameter('patient', $patient);
        return $query->getResult();
    }
    public function searchstatusattente($patient= null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.datePrise,  o.statusVaccin, pr.firstName, pr.lastName, v.vaccinName, v.vaccinDescription FROM App\Entity\OrdoVaccination o 
        INNER JOIN App\Entity\Patient p with p.id= o.patient
        INNER JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
        INNER JOIN App\Entity\Praticien pr with pr.id=d.praticien
        INNER JOIN App\Entity\Vaccin v with v.id=o.vaccin
         WHERE p.id= :patient AND o.statusVaccin= :attente ')
            ->setParameter('attente', "En attente")

            ->setParameter('patient', $patient);
        return $query->getResult();
    }

    public function searchstatusannuler($patient= null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.datePrise,  o.statusVaccin, pr.firstName, pr.lastName, v.vaccinName, v.vaccinDescription FROM App\Entity\OrdoVaccination o 
        INNER JOIN App\Entity\Patient p with p.id= o.patient
        INNER JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
        INNER JOIN App\Entity\Praticien pr with pr.id=d.praticien
        INNER JOIN App\Entity\Vaccin v with v.id=o.vaccin
         WHERE p.id= :patient AND o.statusVaccin= :annuler ')
            ->setParameter('annuler', "Annuler")

            ->setParameter('patient', $patient);
        return $query->getResult();
    }

    // /**
    //  * @return OrdoVaccination[] Returns an array of OrdoVaccination objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrdoVaccination
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
