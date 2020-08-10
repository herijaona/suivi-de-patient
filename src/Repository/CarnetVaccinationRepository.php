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
        $query = $entityManager->createQuery('SELECT c.id,c.datePriseInitiale,c.rappelVaccin, c.etat, p.firstName, p.lastName,v.vaccinName
            FROM App\Entity\CarnetVaccination c 
            INNER JOIN App\Entity\Patient p with p.id = c.patient
            LEFT JOIN App\Entity\Vaccin v with v.id = c.vaccin
            WHERE (p.id = :patient OR p.id IS NULL) AND (c.datePriseInitiale >= :now OR c.rappelVaccin >= :now) 
            ORDER BY c.datePriseInitiale DESC')
            ->setParameter('patient', $patient)
            ->setParameter('now', new \DateTime());

        return $query->getResult();
    }


    public function findListVaccinsInCarnet(Patient $patient){
        $result = [];
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT p.firstName as firstname, p.lastName as lastname, v.vaccinName as vaccin, c.etat as vaccinState, c.datePriseInitiale as datePriseInitiale, c.rappelVaccin as rappel
            FROM App\Entity\CarnetVaccination c
             INNER JOIN App\Entity\Patient p with p.id = c.patient
            INNER JOIN App\Entity\Vaccin v with v.id=c.vaccin
            WHERE p.id= :patient AND (c.datePriseInitiale >= :now OR c.rappelVaccin >= :now)')
            ->setParameter('patient', $patient)
            ->setParameter('now', new \DateTime());

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
