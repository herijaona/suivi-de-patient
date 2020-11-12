<?php

namespace App\Repository;

use App\Entity\CarnetVaccination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Patient;

/**
 * @method CarnetVaccination|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarnetVaccination|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarnetVaccination[]    findAll()
 * @method CarnetVaccination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarnetVaccinationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarnetVaccination::class);
    }


    public function searchCarnet($patient = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT c.id ,c.date_prise, c.etat,v.vaccinName,c.identification,c.identifiant_vaccin,c.Lot,pr.lastName as vaccinateur_nom,pr.firstName as vaccinateur_prenom, pr.NumeroProfessionnel,c.status,v.id as vaccin
            FROM App\Entity\CarnetVaccination c 
            INNER JOIN App\Entity\Patient p with p.id = c.patient
            INNER JOIN App\Entity\Vaccin v with v.id = c.vaccin
            LEFT JOIN App\Entity\Praticien pr with pr.id = c.Praticien
            WHERE (p.id = :patient OR p.id IS NULL) 
            ORDER BY v.vaccinName ASC ')
            ->setParameter('patient', $patient);

        return $query->getResult();
    }

    public function searchCarr($patient = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT c.id,c.date_prise, c.etat,v.vaccinName,c.identification,c.identifiant_vaccin,c.Lot,pr.lastName as vaccinateur_nom,pr.firstName as vaccinateur_prenom, pr.NumeroProfessionnel,c.status
            FROM App\Entity\CarnetVaccination c 
            INNER JOIN App\Entity\Patient p with p.id = c.patient
            INNER JOIN App\Entity\Vaccin v with v.id = c.vaccin
            LEFT JOIN App\Entity\Praticien pr with pr.id = c.Praticien
            WHERE (p.id = :patient OR p.id IS NULL) AND (c.vaccin= 56 AND c.date_prise = p.DateEnceinte)
            ORDER BY v.vaccinName ASC ')
            ->setParameter('patient', $patient);

        return $query->getResult();
    }


    public function findListVaccinsInCarnet(Patient $patient){
        $result = [];
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(' SELECT c.id, p.firstName , p.lastName , v.vaccinName, c.date_prise, c.identifiant_vaccin,c.identification
            FROM App\Entity\CarnetVaccination c
             INNER JOIN App\Entity\Patient p with p.id = c.patient
            INNER JOIN App\Entity\Vaccin v with v.id=c.vaccin
            WHERE p.id= :patient ')
            ->setParameter('patient', $patient);

        return $query->getResult();
    }



    public function findvaccin($praticien = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT count(distinct p.id) as patient, v.vaccinName as vaccin 
            FROM App\Entity\CarnetVaccination c
            INNER JOIN App\Entity\Patient p with p.id = c.patient
            INNER JOIN App\Entity\Praticien pr with pr.id = c.Praticien
            LEFT JOIN App\Entity\Vaccin v with v.id = c.vaccin
            WHERE pr.id= :praticien '
        )
        ->setParameter('praticien', $praticien);

        return $query->getResult();

    }




    // /**
    //  * @return CarnetVaccination[] Returns an array of CarnetVaccination objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CarnetVaccination
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
