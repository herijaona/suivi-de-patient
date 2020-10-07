<?php

namespace App\Repository;

use App\Entity\Fonction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fonction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fonction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fonction[]    findAll()
 * @method Fonction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FonctionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fonction::class);
    }
    public function searchcountry($fonction = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT s.id, s.nameState
            FROM App\Entity\Fonction f
            INNER JOIN App\Entity\State s with s.id = f.state
            WHERE f.id = :fonction')
            ->setParameter('fonction', $fonction);

        return $query->getResult();
    }
    public function searchcity($fonction = null, $state= null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT c.id, c.nameCity
            FROM App\Entity\Fonction f
            INNER JOIN App\Entity\City c with c.id = f.city
            LEFT JOIN App\Entity\Region r with r.id = c.region
            LEFT JOIN App\Entity\State s with s.id = r.state
            WHERE f.id = :fonction AND s.id =:state')
            ->setParameter('fonction', $fonction)
            ->setParameter('state', $state)
        ;

        return $query->getResult();
    }
    public function searchpraticien($fonction = null, $state= null, $city= null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id, p.lastName, p.firstName
            FROM App\Entity\Fonction f
            INNER JOIN App\Entity\Praticien p with p.id = f.Praticien
            INNER JOIN App\Entity\City c with c.id = f.city
            LEFT JOIN App\Entity\Region r with r.id = c.region
            LEFT JOIN App\Entity\State s with s.id = r.state
            WHERE f.id = :fonction AND s.id =:state AND c.id =:city')
            ->setParameter('fonction', $fonction)
            ->setParameter('state', $state)
            ->setParameter('city', $city)
        ;

        return $query->getResult();
    }
    public function searchp(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id, p.lastName, p.firstName,f.fonction,p.id as praticien
            FROM App\Entity\Fonction f
            INNER JOIN App\Entity\Praticien p with p.id = f.Praticien
          ');

        return $query->getResult();
    }



    // /**
    //  * @return Fonction[] Returns an array of Fonction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fonction
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
