<?php

namespace App\Repository;

use App\Entity\Associer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Associer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Associer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Associer[]    findAll()
 * @method Associer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssocierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Associer::class);
    }
    public function searchAssocier($praticien = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.lastName , p.firstName, p.id as patient
            FROM App\Entity\Associer a
            LEFT JOIN App\Entity\Patient p with p.id=a.patient
            LEFT JOIN App\Entity\Praticien pr with pr.id=a.praticien
             WHERE (pr.id= :praticien OR pr.id IS NULL)')

            ->setParameter('praticien', $praticien);
        return $query->getResult();
    }


    public function findByExampleField($patient)
    {
        return $this->createQueryBuilder('a')
            ->join('v.patient','p')
            ->where('p.id =:p')
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function searchPatient($praticien = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT count(p.id)
            FROM App\Entity\Associer a
            INNER JOIN App\Entity\Patient p with p.id = a.patient
            LEFT JOIN App\Entity\Praticien pr with pr.id = a.praticien
            WHERE pr.id = :praticien')
            ->setParameter('praticien', $praticien);

        return $query->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Associer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
