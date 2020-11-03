<?php

namespace App\Repository;

use App\Entity\GroupFamily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GroupFamily|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupFamily|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupFamily[]    findAll()
 * @method GroupFamily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupFamilyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupFamily::class);
    }

    public function searchGrouF($patient = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery("SELECT  g.id,g.designation,p.lastName,p.firstName
            FROM App\Entity\GroupFamily g 
            INNER JOIN App\Entity\Patient p with p.id = g.patient
       
            WHERE p.id = :patient ")
            ->setParameter('patient', $patient);
        return $query->getResult();

    }

    // /**
    //  * @return GroupFamily[] Returns an array of GroupFamily objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupFamily
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
