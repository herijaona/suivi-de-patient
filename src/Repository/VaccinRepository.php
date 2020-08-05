<?php

namespace App\Repository;

use App\Entity\Vaccin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vaccin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vaccin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vaccin[]    findAll()
 * @method Vaccin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VaccinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vaccin::class);
    }

    // /**
    //  * @return Vaccin[] Returns an array of Vaccin objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Vaccin
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findVaccinByTYpe($typeVAcccin, $State = null)
    {
        $query = $this->createQueryBuilder('v')
            ->distinct(true)
            ->join('v.TypeVaccin', 'tv')
            ->leftJoin('v.vaccinCentreHealths', 'ch')
            ->leftJoin('ch.centreHealth', 'ct')
            ->leftJoin('ct.city', 'c')
            ->leftJoin('c.region', 'r')
            ->leftJoin('r.state', 's')
            ->where('tv.typeName = :tpv')
            ->setParameter('tpv', $typeVAcccin);

            if ($State != null){
                $query->andWhere('s.nameState = :ns')
                    ->setParameter('ns', $State);
            }

            $query->orderBy('v.id', 'ASC');
        return $query->getQuery()->getResult();
    }
}
