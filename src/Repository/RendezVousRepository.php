<?php

namespace App\Repository;

use App\Entity\RendezVous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RendezVous|null find($id, $lockMode = null, $lockVersion = null)
 * @method RendezVous|null findOneBy(array $criteria, array $orderBy = null)
 * @method RendezVous[]    findAll()
 * @method RendezVous[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    // /**
    //  * @return RendezVous[] Returns an array of RendezVous objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RendezVous
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findRdvBy($praticien, $type, $status = 0)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT r
            FROM App\Entity\RendezVous r
            LEFT JOIN App\Entity\Praticien p with p.id = r.praticien
            LEFT JOIN App\Entity\Patient pp with pp.id = r.patient
            WHERE (p.id = :praticien OR p.id IS NULL) AND r.type = :type AND r.status = :status
            ORDER BY r.dateRdv ASC')
            ->setParameter('praticien', $praticien)
            ->setParameter('type', $type)
            ->setParameter('status', $status);
        return $query->getResult();
    }


    public function findCalendarPraticien($praticien, $status = 0)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT r
            FROM App\Entity\RendezVous r
            LEFT JOIN App\Entity\Praticien p with p.id = r.praticien
            LEFT JOIN App\Entity\Patient pp with pp.id = r.patient
            WHERE p.id = :praticien  AND r.status = :status
            ORDER BY r.dateRdv ASC')
            ->setParameter('praticien', $praticien)
            ->setParameter('status', $status);
        return $query->getResult();
    }

    public function findNotification($type, $status = 0)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT r
            FROM App\Entity\RendezVous r
            LEFT JOIN App\Entity\Praticien p with p.id = r.praticien
            LEFT JOIN App\Entity\Patient pp with pp.id = r.patient
            WHERE pp.id IS NULL AND r.type = :type AND r.status = :status AND r.date_rdv >= :now
            ORDER BY r.dateRdv ASC')
            ->setParameter('type', $type)
            ->setParameter('status', $status)
            ->setParameter('now', new \DateTime('now'));
        return $query->getResult();
    }

    public function findRdvByAdmin($type, $status = 0)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT r
            FROM App\Entity\RendezVous r
            LEFT JOIN App\Entity\Praticien p with p.id = r.praticien
            LEFT JOIN App\Entity\Patient pp with pp.id = r.patient
            WHERE r.type = :type AND r.status = :status
            ORDER BY r.dateRdv ASC')
            ->setParameter('type', $type)
            ->setParameter('status', $status);
        return $query->getResult();
    }
}
