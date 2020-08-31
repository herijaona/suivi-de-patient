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


    public function searchGe($patient = null, $status = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.id, o.datePrise,o.etat,o.statusVaccin, pr.firstName, pr.lastName
        FROM App\Entity\OrdoVaccination o 
        INNER JOIN App\Entity\Patient p with p.id= o.patient
        LEFT JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
        LEFT JOIN App\Entity\Praticien pr with pr.id=d.praticien
         WHERE p.id= :patient AND o.statusVaccin= :status AND o.datePrise >= :now
            ORDER BY o.datePrise ASC')
            ->setParameter('status', $status)
            ->setParameter('patient', $patient)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }

    public function searchStatusPraticien($praticien = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT o.id, o.datePrise, o.etat, o.statusVaccin, p.firstName, p.lastName, v.id as vaccin, pr.id as praticien, p.id as patient
            FROM App\Entity\OrdoVaccination o 
            INNER JOIN App\Entity\Patient p with p.id= o.patient
            LEFT JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
            LEFT JOIN App\Entity\Praticien pr with pr.id=d.praticien
            LEFT JOIN App\Entity\Vaccin v with v.id = o.vaccin
            WHERE pr.id= :praticien 
            ORDER BY o.datePrise DESC
        ')->setParameter('praticien', $praticien);

        return $query->getResult();
    }

    public function searchStatusPraticienEnValid($praticien = null, $status = 0, $etat = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT o.id, o.datePrise, o.etat, o.statusVaccin, p.firstName, p.lastName, pr.id as praticien, p.id as patient, v.vaccinName , v.id as vaccin            
            FROM App\Entity\OrdoVaccination o 
            INNER JOIN App\Entity\Patient p with p.id= o.patient
            INNER JOIN App\Entity\Vaccin v with v.id= o.vaccin
            LEFT JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
            LEFT JOIN App\Entity\Praticien pr with pr.id=o.referencePraticienExecutant
            WHERE (pr.id= :praticien OR pr.id IS NULL) AND o.statusVaccin= :status AND o.etat= :etat AND o.datePrise >= :now
            ORDER BY o.datePrise ASC
        ')->setParameter('etat', $etat)
          ->setParameter('status', $status)
          ->setParameter('praticien', $praticien)
          ->setParameter('now', new \DateTime());

        return $query->getResult();
    }

    public function searchStatusPraticienGe($praticien = null, $status = 0, $etat = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT o.id, o.datePrise, o.etat, o.statusVaccin, p.firstName, p.lastName, pr.id as praticien, p.id as patient          
            FROM App\Entity\OrdoVaccination o 
            INNER JOIN App\Entity\Patient p with p.id= o.patient
            LEFT JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
            LEFT JOIN App\Entity\Praticien pr with pr.id=o.referencePraticienExecutant
            WHERE (pr.id= :praticien OR pr.id IS NULL) AND o.statusVaccin= :status AND o.etat= :etat AND o.datePrise >= :now
            ORDER BY o.datePrise ASC
        ')->setParameter('etat', $etat)
            ->setParameter('status', $status)
            ->setParameter('praticien', $praticien)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }
    public function searchStatusPraticienNotif($praticien = null, $status = 0,$statusNotif = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT count(o.id)
        FROM App\Entity\OrdoVaccination o 
        INNER JOIN App\Entity\Patient p with p.id= o.patient
        LEFT JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
        LEFT JOIN App\Entity\Praticien pr with pr.id=d.praticien
        LEFT JOIN App\Entity\Vaccin v with v.id = o.vaccin
         WHERE (pr.id= :praticien OR pr.id IS NULL) AND o.statusVaccin= :status AND o.statusNotif= :etat AND o.datePrise >= :now
         ORDER BY o.datePrise ASC')
            ->setParameter('praticien', $praticien)
            ->setParameter('status', $status)
            ->setParameter('etat', $statusNotif)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }

    public function searchStatusPraticienAll($praticien = null , $statusNotif= 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.id,p.firstName, p.lastName,p.id as patient, pr.id as praticien
            FROM App\Entity\OrdoVaccination o 
            INNER JOIN App\Entity\Patient p with p.id= o.patient
            LEFT JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
            LEFT JOIN App\Entity\Praticien pr with pr.id=d.praticien
            LEFT JOIN App\Entity\Vaccin v with v.id = o.vaccin
            WHERE (pr.id = :praticien OR pr.id IS NULL)  AND o.statusNotif= :etat
            ORDER BY o.datePrise ASC')
            ->setParameter('praticien', $praticien)
            ->setParameter('etat', $statusNotif);

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

    public function getQueryVacc()
    {

        $qb = $this->createQueryBuilder('o');
        return $qb->select('
            COUNT(o.id)  AS nb_vaccin, 
            MONTH(o.datePrise) AS month, 
            YEAR(o.datePrise) AS year
            ')
            ->join("o.vaccin", "v")
            ->where('o.statusVaccin = 1')
            ->groupBy('month')
            ->addGroupBy('year')
            ->orderBy('o.datePrise', 'ASC')
            ->getQuery()
            ->getResult();

    }

// --------------------------------------------------------------------------------------------
// Get current user/praticien patients birthday
// --------------------------------------------------------------------------------------------
    // SELECT patient.date_on_born
    // FROM patient
    // INNER JOIN ordo_vaccination
    // INNER JOIN praticien
    // INNER JOIN user
    // WHERE ordo_vaccination.patient_id = patient.id
    // AND praticien.id = ordo_vaccination.reference_praticien_executant_id
    // AND user.id = praticien.user_id
    // AND user.id = 2
// --------------------------------------------------------------------------------------------
    public function findPatientsBirthday($userId){
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery("
            SELECT p.dateOnBorn as birthday
            FROM App\Entity\Patient p
            INNER JOIN App\Entity\OrdoVaccination o WITH o.patient = p.id
            INNER JOIN App\Entity\Praticien pr WITH pr.id = o.referencePraticienExecutant
            INNER JOIN App\Entity\User u WITH u.id = pr.user
            WHERE u.id = :userId
            AND o.statusVaccin = 1
        ")->setParameter('userId', $userId);

        return $query->getResult();
    }
// --------------------------------------------------------------------------------------------
}
