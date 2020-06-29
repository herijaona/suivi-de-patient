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
