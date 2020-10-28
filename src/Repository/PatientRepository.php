<?php

namespace App\Repository;

use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

    public function searchPatient($user = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id,p.firstName,p.lastName,t.id as typePatient,u.username,u.email,s.id as namestate,c.id as nameCity,p.address,i.id as cityBorn,a.id as countryBorn,p.createdAt,p.dateOnBorn,p.motherName,p.fatherName,p.updatedAt,p.phone,p.sexe
            FROM App\Entity\Patient p 
            INNER JOIN App\Entity\User u with u.id = p.user
            LEFT JOIN App\Entity\State s with s.id = p.state
            LEFT JOIN App\Entity\State a with a.id = p.CountryOnborn
            LEFT JOIN App\Entity\City c with c.id = p.city
            LEFT JOIN App\Entity\City i with  i.id = p.CityOnBorn
            LEFT JOIN App\Entity\TypePatient t with t.id= p.typePatient
            WHERE u.id = :user ')
            ->setParameter('user', $user);
        return $query->getResult();
    }
    public function sr($user = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT  p.isEnceinte
            FROM App\Entity\Patient p 
            INNER JOIN App\Entity\User u with u.id = p.user
            WHERE u.id = :user and (p.isEnceinte = true )')
            ->setParameter('user', $user);
        return $query->getResult();

    }
   /** public function searchTypePatient($patient = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT  p.id,t.typePatientName,p.sexe,p.isEnceinte
            FROM App\Entity\Patient p 
            INNER JOIN App\Entity\TypePatient t with t.id = p.typePatient
            WHERE p.id = :patient 
            ORDER BY p.id ASC')
            ->setParameter('patient', $patient);
        return $query->getResult();
    } **/

    // /**
    //  * @return Patient[] Returns an array of Patient objects
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
    public function findOneBySomeField($value): ?Patient
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByPatient()
    {
        return $this->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByUser()
    {
        return $this->createQueryBuilder('p')
            ->join('p.patient', 'u')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByPatientId($user){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT r.id
            FROM App\Entity\Patient r
            INNER JOIN App\Entity\User c with c.id = r.user 
            WHERE (c.id = :user) ')
            ->setParameter('user', $user);
        return $query->getArrayResult();
    }

    public function findByPatientUser($user){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT r
            FROM App\Entity\Patient r
            INNER JOIN App\Entity\User c with c.id = r.user 
            WHERE (c.id = :user) ')
            ->setParameter('user', $user);
        return $query->getResult();
    }

    public function findNbrPatientGroupByType(){
        $qb = $this->createQueryBuilder('p');
        return $qb->select('
            COUNT(p.id)  AS nb_patient, 
            tp.id AS tpId, 
            tp.typePatientName AS typePatientName
            ')
            ->join("p.typePatient", "tp")
            ->where('p.etat = 1')
            ->groupBy('tp.id')
            ->orderBy('tp.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


}
