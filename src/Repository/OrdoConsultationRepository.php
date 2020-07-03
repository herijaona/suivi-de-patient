<?php

namespace App\Repository;

use App\Entity\OrdoConsultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrdoConsultation|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdoConsultation|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdoConsultation[]    findAll()
 * @method OrdoConsultation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdoConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdoConsultation::class);
    }

    public function searchstatusvalider($patient= null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.dateRdv, o.objetConsultation, o.statusConsultation,o.referencePraticientExecutant,o.typePraticien,pr.firstName, pr.lastName FROM App\Entity\OrdoConsultation o 
        INNER JOIN App\Entity\Patient p with p.id= o.patient
        INNER JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
        INNER JOIN App\Entity\Praticien pr with pr.id=d.praticien
         WHERE p.id= :patient AND o.statusConsultation= :valider ')
            ->setParameter('valider', "Valider")

            ->setParameter('patient', $patient);
        return $query->getResult();
    }
    public function searchstatusattente($patient= null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.dateRdv, o.objetConsultation, o.statusConsultation,o.referencePraticientExecutant,o.typePraticien,pr.firstName, pr.lastName FROM App\Entity\OrdoConsultation o 
        INNER JOIN App\Entity\Patient p with p.id= o.patient
        INNER JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
        INNER JOIN App\Entity\Praticien pr with pr.id=d.praticien
         WHERE p.id= :patient AND o.statusConsultation= :attente ')
            ->setParameter('attente', "En attente")

            ->setParameter('patient', $patient);
        return $query->getResult();
    }

    public function searchstatusannuler($patient= null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT o.dateRdv, o.objetConsultation, o.statusConsultation,o.referencePraticientExecutant,o.typePraticien,pr.firstName, pr.lastName FROM App\Entity\OrdoConsultation o 
        INNER JOIN App\Entity\Patient p with p.id= o.patient
        INNER JOIN App\Entity\Ordonnace d with d.id=o.ordonnance
        INNER JOIN App\Entity\Praticien pr with pr.id=d.praticien
         WHERE p.id= :patient AND o.statusConsultation= :annuler')
            ->setParameter('annuler', "Annuler")

            ->setParameter('patient', $patient);
        return $query->getResult();
    }




    // /**
    //  * @return OrdoConsultation[] Returns an array of OrdoConsultation objects
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
    public function findOneBySomeField($value): ?OrdoConsultation
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
