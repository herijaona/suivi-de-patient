<?php

namespace App\Repository;

use App\Entity\CentreHealth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CentreHealth|null find($id, $lockMode = null, $lockVersion = null)
 * @method CentreHealth|null findOneBy(array $criteria, array $orderBy = null)
 * @method CentreHealth[]    findAll()
 * @method CentreHealth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CentreHealthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CentreHealth::class);
    }
    public function searchCentre($city = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT c.id, c.centreName
            FROM App\Entity\CentreHealth c
            LEFT JOIN App\Entity\City s with s.id = c.city
            WHERE s.id = :city')
            ->setParameter('city', $city);

        return $query->getResult();
    }


        // /**
    //  * @return CentreHealth[] Returns an array of CentreHealth objects
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
    public function findOneBySomeField($value): ?CentreHealth
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
