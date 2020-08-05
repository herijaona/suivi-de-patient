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

    public function searchIntervationPraticien($praticien = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT i.id, i.dateConsultation,i.etat,p.firstName as patient_name, p.lastName as patient_lastname, pr.firstName,pr.lastName,o.objetConsultation
            FROM App\Entity\IntervationConsultation i 
            INNER JOIN App\Entity\Patient p with p.id = i.patient
            LEFT JOIN App\Entity\OrdoConsultation o with o.id = i.ordoConsulataion
            LEFT JOIN App\Entity\Praticien pr with pr.id = i.praticienPrescripteur
            WHERE pr.id = :praticien AND i.dateConsultation >= :now
            ORDER BY i.dateConsultation ASC')
            ->setParameter('praticien', $praticien)
            ->setParameter('now', new \DateTime());

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
