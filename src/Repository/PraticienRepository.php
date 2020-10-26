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
        $query = $entityManager->createQuery('SELECT p.lastName, p.firstName, f.fonction
            FROM App\Entity\Praticien p
            INNER JOIN App\Entity\Fonction f with f.id = p.fonctions
        
          
            ')
         ;
        return $query->getResult();
    }

    public function searchPr($user = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id,p.firstName,p.lastName,p.dateBorn,p.NumeroProfessionnel,p.phone,p.address,p.sexe,p.updatedAt,p.createdAt,u.email,u.username,s.id as countryBorn, c.id as cityBorn, st.id as countryFonction,ci.id as CityFonction, f.id as fonction
            FROM App\Entity\Praticien p
            INNER JOIN App\Entity\User u with u.id = p.user
            LEFT JOIN App\Entity\State s with s.id =p.CountryOnBorn
            LEFT JOIN App\Entity\City c with c.id = p.CityOnBorn
            LEFT JOIN App\Entity\State st with st.id =p.CountryFonction
            LEFT JOIN App\Entity\City ci with ci.id = p.CityFonction
            LEFT JOIN App\Entity\Fonction f with f.id = p.Fonction
            
            where u.id =:user
            ')
            ->setParameter('user', $user);
        return $query->getResult();
    }

    public function searchcount($fonction = null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT DISTINCT s.id, s.nameState
            FROM App\Entity\Praticien p
            INNER JOIN App\Entity\State s with s.id = p.CountryFonction
            INNER JOIN App\Entity\Fonction f with f.id = p.Fonction
            WHERE f.id = :fonction')
            ->setParameter('fonction', $fonction);

        return $query->getResult();
    }

    public function searchcity($fonction = null, $state= null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT DISTINCT c.id, c.nameCity
            FROM App\Entity\Praticien p
            INNER JOIN App\Entity\City c with c.id = p.CityFonction
            INNER JOIN App\Entity\Fonction f with f.id = p.Fonction
            LEFT JOIN App\Entity\State s with s.id = p.CountryFonction
            WHERE f.id = :fonction AND s.id =:state')
            ->setParameter('fonction', $fonction)
            ->setParameter('state', $state)
        ;

        return $query->getResult();
    }
    public function searchpra($fonction = null, $state= null, $city= null){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p.id, p.lastName, p.firstName
            FROM App\Entity\Praticien p
            INNER JOIN App\Entity\City c with c.id = p.CityFonction
            INNER JOIN App\Entity\Fonction f with f.id = p.Fonction
            LEFT JOIN App\Entity\State s with s.id = p.CountryFonction
            WHERE f.id = :fonction AND s.id =:state AND c.id =:city')
            ->setParameter('fonction', $fonction)
            ->setParameter('state', $state)
            ->setParameter('city', $city)
        ;

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
