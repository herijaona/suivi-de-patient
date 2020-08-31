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
    public function searchStatus($patient = null, $status = 0, $etat = 0, $type= 'consultation'){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id, p.dateProposition,p.descriptionProposition,p.statusProposition,p.etat,pr.firstName , pr.lastName
            FROM App\Entity\PropositionRdv p
            LEFT JOIN App\Entity\Praticien pr with pr.id = p.praticien
            LEFT JOIN App\Entity\Patient pa with pa.id = p.patient
            WHERE(pa.id = :patient OR pa.id IS NULL) AND p.dateProposition >= :now  AND p.statusProposition = :status AND p.etat = :etat AND p.type =:type
            ORDER BY p.dateProposition ASC')
            ->setParameter('status', $status)
            ->setParameter('etat', $etat)
            ->setParameter('type', $type)
            ->setParameter('patient', $patient)
            ->setParameter('now', new \DateTime());
        return $query->getResult();
    }
    public function searchStat($patient = null, $status = 0, $etat = 0,$type = 'vaccination'){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id, p.dateProposition,p.descriptionProposition,p.statusProposition,p.etat,pr.firstName , pr.lastName, v.vaccinName
            FROM App\Entity\PropositionRdv p
            INNER JOIN App\Entity\Vaccin v with v.id = p.vaccin
            LEFT JOIN App\Entity\Praticien pr with pr.id = p.praticien
            LEFT JOIN App\Entity\Patient pa with pa.id = p.patient
            WHERE(pa.id = :patient OR pa.id IS NULL) AND p.dateProposition >= :now  AND p.statusProposition = :status AND p.etat = :etat AND p.type = :type
            ORDER BY p.dateProposition ASC')
            ->setParameter('status', $status)
            ->setParameter('etat', $etat)
            ->setParameter('type', $type)
            ->setParameter('patient', $patient)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }

    public function searchProposition($patient= null , $type= 'vaccination'){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id, p.dateProposition,p.statusProposition ,p.descriptionProposition, pr.firstName, pr.lastName, pr.id as praticien, pa.firstName as patientfirst, pa.lastName as patientlast, pa.id as patient, v.vaccinName,p.type 
            FROM App\Entity\PropositionRdv p
            INNER JOIN APP\Entity\Vaccin v with v.id= p.vaccin
            LEFT JOIN App\Entity\Praticien pr with pr.id = p.praticien
            LEFT JOIN App\Entity\Patient pa with pa.id = p.patient
            WHERE(pa.id = :patient OR pa.id IS NULL) AND p.dateProposition >= :now AND p.type =:type
            ORDER BY p.dateProposition ASC')
            ->setParameter('now', new \DateTime())
            ->setParameter('patient', $patient)
            ->setParameter('type', $type);

        return $query->getResult();
    }
    public function searchPropositio($patient= null, $type = 'consultation'){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id, p.dateProposition,p.statusProposition ,p.descriptionProposition, pr.firstName, pr.lastName, pr.id as praticien, pa.firstName as patientfirst, pa.lastName as patientlast, pa.id as patient,p.type
            FROM App\Entity\PropositionRdv p
            LEFT JOIN App\Entity\Praticien pr with pr.id = p.praticien
            LEFT JOIN App\Entity\Patient pa with pa.id = p.patient
            WHERE(pa.id = :patient OR pa.id IS NULL) AND p.dateProposition >= :now AND p.type =:type
            ORDER BY p.dateProposition ASC')
            ->setParameter('now', new \DateTime())
            ->setParameter('patient', $patient)
            ->setParameter('type', $type);

        return $query->getResult();
    }
    public function searchStatusPraticienEnValid($praticien = null, $status = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT distinct p.id, p.dateProposition, p.descriptionProposition, pa.lastName, pa.firstName
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

    public function searchStatusPraticienv($praticien = null, $status = 0){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT distinct p.id, p.dateProposition, pa.lastName, pa.firstName, v.vaccinName
            FROM App\Entity\PropositionRdv p
            INNER JOIN App\Entity\Vaccin v with v.id = p.vaccin
            LEFT JOIN App\Entity\Praticien pr with pr.id = p.praticien
            LEFT JOIN App\Entity\Patient pa with pa.id = p.patient
            WHERE (pr.id = :praticien OR pr.id IS NULL) AND p.statusProposition = :status AND p.dateProposition >= :now 
            ORDER BY p.dateProposition ASC')
            ->setParameter('status', $status)
            ->setParameter('praticien', $praticien)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }
    public function searchStatusPraticien($praticien = null,$status =1,$type = 'consultation'){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id,p.etat, p.dateProposition, p.descriptionProposition, pa.lastName, pa.firstName,p.statusProposition
            FROM App\Entity\PropositionRdv p
            LEFT JOIN App\Entity\Praticien pr with pr.id = p.praticien
            LEFT JOIN App\Entity\Patient pa with pa.id = p.patient
            WHERE (pr.id = :praticien OR pr.id IS NULL) AND p.statusProposition =:status AND p.type =:type
            ORDER BY p.dateProposition ASC')
            ->setParameter('praticien', $praticien)
            ->setParameter('type', $type)
            ->setParameter('status', $status);
        return $query->getResult();
    }
    public function searchSta($praticien = null,$status = 1,$type = 'vaccination'){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id,p.etat, p.dateProposition, pa.lastName, pa.firstName,p.statusProposition,v.vaccinName
            FROM App\Entity\PropositionRdv p
            INNER JOIN App\Entity\Vaccin v with v.id = p.vaccin
            LEFT JOIN App\Entity\Praticien pr with pr.id = p.praticien
            LEFT JOIN App\Entity\Patient pa with pa.id = p.patient
            WHERE (pr.id = :praticien OR pr.id IS NULL) AND p.statusProposition =:status AND p.type =:type
            ORDER BY p.dateProposition ASC')
            ->setParameter('praticien', $praticien)
            ->setParameter('type', $type)
            ->setParameter('status', $status);
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
