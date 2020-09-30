<?php

namespace App\Repository;

use App\Entity\IntervationConsultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IntervationConsultation|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntervationConsultation|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntervationConsultation[]    findAll()
 * @method IntervationConsultation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntervationConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntervationConsultation::class);
    }


    public function searchStatusInter($patient = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.id, o.dateConsultation, o.objetConsultation,o.etat, o.status,pr.firstName, pr.lastName,c.centreName
            FROM App\Entity\IntervationConsultation o 
            INNER JOIN App\Entity\Patient p with p.id = o.patient
            LEFT JOIN App\Entity\Ordonnace d with d.id = o.ordonnace
            LEFT JOIN App\Entity\Praticien pr with pr.id = d.praticien
            LEFT JOIN App\Entity\CentreHealth c with c.id = d.CentreSante
            WHERE p.id = :patient  
            ORDER BY o.dateConsultation ASC')
            ->setParameter('patient', $patient)
           ;

        return $query->getResult();
    }

    public function searchIn($praticien = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.id, o.dateConsultation, o.objetConsultation,o.etat, o.status,p.firstName, p.lastName
                FROM App\Entity\IntervationConsultation o 
                INNER JOIN App\Entity\Patient p with p.id = o.patient
                LEFT JOIN App\Entity\Ordonnace d with d.id = o.ordonnace
                LEFT JOIN App\Entity\Praticien pr with pr.id = d.praticien
                LEFT JOIN App\Entity\CentreHealth c with c.id = d.CentreSante
                WHERE pr.id = :praticien  
                ORDER BY o.dateConsultation ASC')
            ->setParameter('praticien', $praticien)
        ;

        return $query->getResult();
    }


    public function searchPatient($praticien = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT count(p.id)
            FROM App\Entity\IntervationConsultation i 
            INNER JOIN App\Entity\Patient p with p.id = i.patient
            LEFT JOIN App\Entity\OrdoConsultation o with o.id = i.ordoConsulataion
            LEFT JOIN App\Entity\Praticien pr with pr.id = i.praticienPrescripteur
            WHERE pr.id = :praticien
            ORDER BY i.dateConsultation ASC')
            ->setParameter('praticien', $praticien);

        return $query->getResult();
    }


    // /**
    //  * @return IntervationConsultation[] Returns an array of IntervationConsultation objects
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
    public function findOneBySomeField($value): ?IntervationConsultation
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
