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
            ->where('tv.typeName = :tpv')
            ->setParameter('tpv', $typeVAcccin)
            ->orderBy('v.id', 'ASC');
        return $query->getQuery()->getResult();
    }

    public function countPriseVaccinParType(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT tv.typeName as typeVaccin, COUNT(tv.id) as nb
            FROM App\Entity\TypeVaccin tv
            INNER JOIN App\Entity\Vaccin v WITH v.TypeVaccin = tv.id
            INNER JOIN App\Entity\CarnetVaccination cv WITH cv.vaccin = v.id
            GROUP BY tv.id
        ');
        return $query->getResult();
    }
}
