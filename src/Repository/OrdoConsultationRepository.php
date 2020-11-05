<?php

namespace App\Repository;

use App\Entity\OrdoConsultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrdoConsultation|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdoConsultation|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdoConsultation[]    findAll()
 * @method OrdoConsultation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdoConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdoConsultation::class);
    }

    public function searchStatus($patient = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.id, o.dateRdv, o.objetConsultation,o.etat, o.statusConsultation,pr.firstName, pr.lastName,c.centreName
            FROM App\Entity\OrdoConsultation o 
            INNER JOIN App\Entity\Patient p with p.id = o.patient
            LEFT JOIN App\Entity\Ordonnace d with d.id = o.ordonnance
            LEFT JOIN App\Entity\Praticien pr with pr.id = d.praticien
            LEFT JOIN App\Entity\CentreHealth c with c.id = d.CentreSante
            WHERE p.id = :patient
            ORDER BY o.dateRdv ASC')
            ->setParameter('patient', $patient)
        ;

        return $query->getResult();
    }

    public function searchStatusPraticien($praticien = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.id, o.dateRdv, o.objetConsultation,o.etat,o.statusConsultation,p.firstName, p.lastName,p.id as patient, pr.id as praticien
            FROM App\Entity\OrdoConsultation o 
            INNER JOIN App\Entity\Patient p with p.id = o.patient
            LEFT JOIN App\Entity\Ordonnace d with d.id = o.ordonnance
            LEFT JOIN App\Entity\Praticien pr with pr.id = d.praticien
            WHERE pr.id = :praticien 
            ORDER BY o.dateRdv ASC')
            ->setParameter('praticien', $praticien);

        return $query->getResult();
    }

    public function searchStatusPraticienEnValid($praticien = null, $status = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.id, o.dateRdv, o.objetConsultation, o.statusConsultation,o.referencePraticientExecutant,o.typePraticien,p.firstName, p.lastName,p.id as patient, pr.id as praticien
            FROM App\Entity\OrdoConsultation o 
            INNER JOIN App\Entity\Patient p with p.id = o.patient
            LEFT JOIN App\Entity\Ordonnace d with d.id = o.ordonnance
            LEFT JOIN App\Entity\Praticien pr with pr.id = d.praticien
            WHERE (pr.id = :praticien OR pr.id IS NULL) AND o.statusConsultation = :status AND o.dateRdv >= :now
            ORDER BY o.dateRdv ASC')
            ->setParameter('status', $status)
            ->setParameter('praticien', $praticien)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }
    public function  searchConsultation($praticien){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT count(o.id)
        FROM App\Entity\OrdoConsultation o 
        LEFT JOIN App\Entity\Ordonnace d with d.id = o.ordonnance
        LEFT JOIN App\Entity\Praticien pr with pr.id = d.praticien
        WHERE (pr.id = :praticien OR pr.id IS NULL)
        ')
            ->setParameter('praticien', $praticien);
        return $query->getResult();
    }
    public function searchCons(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.id, o.dateRdv, o.objetConsultation, o.statusConsultation,pr.firstName, pr.lastName,p.firstName as patientfirst,p.lastName as patientlast
            FROM App\Entity\OrdoConsultation o 
            INNER JOIN App\Entity\Patient p with p.id = o.patient
            LEFT JOIN App\Entity\Ordonnace d with d.id = o.ordonnance
            LEFT JOIN App\Entity\Praticien pr with pr.id = d.praticien
            ORDER BY o.dateRdv ASC');
        return $query->getResult();
    }

    public function countUnrealizedrdv($praticien){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT COUNT(o.id)
            FROM App\Entity\OrdoConsultation o
            INNER JOIN App\Entity\Ordonnace od with od.id = o.ordonnance
            INNER JOIN App\Entity\Praticien p with p.id = od.praticien
            WHERE o.statusConsultation = :etat AND p.id = :praticien
        ')->setParameter('etat', 0)
            ->setParameter('praticien', $praticien);
        return $query->getResult();
    }
    public function countPatient($praticien){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT COUNT(pa.id)
            FROM App\Entity\OrdoConsultation o
            INNER JOIN App\Entity\Ordonnace od with od.id = o.ordonnance
            INNER JOIN App\Entity\Praticien p with p.id = od.praticien
            INNER JOIN App\Entity\Patient pa with pa.id = o.patient
            WHERE p.id = :praticien
        ')
            ->setParameter('praticien', $praticien);
        return $query->getResult();
    }





    // /**
    //  * @return OrdoConsultation[] Returns an array of OrdoConsultation objects
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
    public function findOneBySomeField($value): ?OrdoConsultation
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
