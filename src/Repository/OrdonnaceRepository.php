<?php

namespace App\Repository;

use App\Entity\Ordonnace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ordonnace|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ordonnace|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ordonnace[]    findAll()
 * @method Ordonnace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdonnaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ordonnace::class);
    }
    public function searchPcent($centre = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id, p.firstName, p.lastName, p.fonction
            FROM App\Entity\Ordonnace o
            INNER  JOIN  App\Entity\Praticien p with p.id = o.praticien
            INNER  JOIN App\Entity\CentreHealth c with c.id = o.CentreSante
            WHERE c.id = :centre')
            ->setParameter('centre', $centre);

        return $query->getResult();
    }

    // /**
    //  * @return Ordonnace[] Returns an array of Ordonnace objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ordonnace
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
