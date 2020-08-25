<?php

namespace App\Repository;

use App\Entity\IntervationMedicale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IntervationMedicale|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntervationMedicale|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntervationMedicale[]    findAll()
 * @method IntervationMedicale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntervationMedicaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntervationMedicale::class);
    }

    // /**
    //  * @return IntervationMedicale[] Returns an array of IntervationMedicale objects
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
    public function findOneBySomeField($value): ?IntervationMedicale
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
