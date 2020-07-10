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
    public function searchIntervationPraticien($praticien = null, $etat = 1){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT i.id, i.datePriseVaccin,i.etat,p.firstName as patient_name,v.vaccinName, p.lastName as patient_lastname, pr.firstName,pr.lastName
            FROM App\Entity\InterventionVaccination i 
            INNER JOIN App\Entity\Patient p with p.id = i.patient
            LEFT JOIN App\Entity\Praticien pr with pr.id = i.praticienPrescripteur
            INNER JOIN App\Entity\Vaccin v with v.id = i.vaccin
            WHERE pr.id = :praticien  AND i.etat = :etat  AND i.datePriseVaccin >= :now
            ORDER BY i.datePriseVaccin ASC')
            ->setParameter('etat', $etat)
            ->setParameter('praticien', $praticien)
            ->setParameter('now', new \DateTime());

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
