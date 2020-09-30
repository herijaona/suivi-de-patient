<?php

namespace App\Repository;

use App\Entity\Praticien;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Praticien|null find($id, $lockMode = null, $lockVersion = null)
 * @method Praticien|null findOneBy(array $criteria, array $orderBy = null)
 * @method Praticien[]    findAll()
 * @method Praticien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PraticienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Praticien::class);
    }
    public function searchPraticien(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id,p.lastName, p.firstName, p.fonction
            FROM App\Entity\Praticien p');
        return $query->getResult();
    }




    // /**
    //  * @return Praticien[] Returns an array of Praticien objects
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
    public function findOneBySomeField($value): ?Praticien
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByPraticien()
    {
        return $this->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByPraticienId($user){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT r.id
            FROM App\Entity\Praticien r
            INNER JOIN App\Entity\User c with c.id = r.user 
            WHERE (c.id = :user) ')
            ->setParameter('user', $user);
        return $query->getArrayResult();
    }

    public function findByPraticienUser($user){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT r
            FROM App\Entity\Praticien r
            INNER JOIN App\Entity\User c with c.id = r.user 
            WHERE (c.id = :user) ')
            ->setParameter('user', $user);
        return $query->getResult();
    }
}
