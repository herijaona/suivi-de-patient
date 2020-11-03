<?php

namespace App\Repository;

use App\Entity\Family;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Family|null find($id, $lockMode = null, $lockVersion = null)
 * @method Family|null findOneBy(array $criteria, array $orderBy = null)
 * @method Family[]    findAll()
 * @method Family[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamilyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Family::class);
    }

    public function  searchj($groupe= null, $patient = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id as patient, f.id , p.lastName, p.firstName
            FROM App\Entity\Family f
            INNER JOIN App\Entity\GroupFamily g with g.id = f.groupFamily
            INNER JOIN App\Entity\Patient p with p.id= f.patientChild
            LEFT JOIN App\Entity\User u with u.id = p.user
            where f.Referent = 1 and g.id= :groupe  and p.id =:patient
          ')
            ->setParameter('groupe', $groupe)
            ->setParameter('patient', $patient)
        ;

        return $query->getResult();
    }


    public function  searchFamily($groupe= null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT f.id,p.lastName,p.firstName,u.username,f.Referent, p.id as patient
            FROM App\Entity\Family f
            INNER JOIN App\Entity\GroupFamily g with g.id = f.groupFamily
            INNER JOIN App\Entity\Patient p with p.id= f.patientChild
            LEFT JOIN App\Entity\User u with u.id = p.user
            where g.id= :groupe
          ')
            ->setParameter('groupe', $groupe);
        return $query->getResult();
    }
    // /**
    //  * @return Family[] Returns an array of Family objects
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
    public function findOneBySomeField($value): ?Family
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByPatientParent($parent = null)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery('SELECT f.id, pp.id as ppid, pc.id as pcid, pp.first_name as pc_first_name, pp.last_name as pc_last_name
            FROM App\Entity\Family f
            LEFT JOIN App\Entity\Patient pp with pp.id = f.patient_parent
            LEFT JOIN App\Entity\Patient pc with pc.id = f.patient_child 
            WHERE pp.id = :parent')
            ->setParameter('parent', $parent);
        return $query->getResult();

    }

    public function getPatientByIdFamily($groupeId, $notPatient) {
        $qb = $this->createQueryBuilder('f');
        $qb->join('f.groupFamily', 'g')
            ->addSelect('g')
            ->join('f.patientChild', 'pc')
            ->addSelect('pc')
            ->add('where', $qb->expr()->eq('g.id', ':groupeId'))
            ->andWhere($qb->expr()->notIn('pc.id',':notPatient'))
            ->setParameter('groupeId', $groupeId)
            ->setParameter('notPatient',  $notPatient);


        return $qb->getQuery()->getResult();
    }
}
