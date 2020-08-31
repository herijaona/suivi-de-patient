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

    public function findVaccinByTYpe($typeVAcccin, $state)
    {
        $query = $this->createQueryBuilder('v')
            ->distinct(true)
            ->join('v.TypeVaccin', 'tv')
            ->join('v.state', 'sv')
            ->where('tv.typeName = :tpv')
            ->andWhere('sv.nameState = :state')
            ->setParameter('tpv', $typeVAcccin)
            ->setParameter('state', $state)
            ->orderBy('v.id', 'ASC');
        return $query->getQuery()->getResult();
    }

    


// -------------------------------------------------------------------------------------------
// If count should not depend on praticien
// -------------------------------------------------------------------------------------------
// SELECT type_vaccin.type_name as "Type de vaccin", COUNT(type_vaccin.id) as "Nombre"
// FROM type_vaccin
// INNER JOIN vaccin
// INNER JOIN carnet_vaccination
// INNER JOIN intervention_vaccination
// INNER JOIN praticien
// WHERE vaccin.type_vaccin_id = type_vaccin.id
// AND carnet_vaccination.vaccin_id = vaccin.id
// AND intervention_vaccination.id = carnet_vaccination.intervation_vaccination_id
// AND praticien.id = intervention_vaccination.praticien_prescripteur_id
// AND praticien.id = 1
// GROUP BY type_vaccin.id
// -------------------------------------------------------------------------------------------
    // public function countPriseVaccinParType(){
    //     $entityManager = $this->getEntityManager();
    //     $query = $entityManager->createQuery('
    //         SELECT tv.typeName as typeVaccin, COUNT(tv.id) as nb
    //         FROM App\Entity\TypeVaccin tv
    //         INNER JOIN App\Entity\Vaccin v WITH v.TypeVaccin = tv.id
    //         INNER JOIN App\Entity\CarnetVaccination cv WITH cv.vaccin = v.id
    //         GROUP BY tv.id
    //     ');
    //     return $query->getResult();
    // }
// -------------------------------------------------------------------------------------------

// -------------------------------------------------------------------------------------------
// If count should depend on praticien (by using its user id)
// ie if only type of vaccins provided by the current user/praticien should be counted
// -------------------------------------------------------------------------------------------
    // SELECT type_vaccin.type_name, COUNT(type_vaccin.id)
    // FROM type_vaccin
    // INNER JOIN vaccin
    // INNER JOIN carnet_vaccination
    // INNER JOIN intervention_vaccination
    // INNER JOIN praticien
    // INNER JOIN user
    // WHERE vaccin.type_vaccin_id = type_vaccin.id
    // AND carnet_vaccination.vaccin_id = vaccin.id
    // AND intervention_vaccination.id = carnet_vaccination.intervation_vaccination_id
    // AND praticien.id = intervention_vaccination.praticien_prescripteur_id
    // AND user.id = praticien.user_id
    // AND user.id = 2
    // AND carnet_vaccination.date_prise_initiale IS NOT NULL
    // GROUP BY type_vaccin.id
// -------------------------------------------------------------------------------------------
    public function countPriseVaccinParType($userId){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('
            SELECT tv.typeName as typeVaccin, COUNT(tv.id) as nb
            FROM App\Entity\TypeVaccin tv
            INNER JOIN App\Entity\Vaccin v WITH v.TypeVaccin = tv.id
            INNER JOIN App\Entity\CarnetVaccination cv WITH cv.vaccin = v.id
            INNER JOIN App\Entity\InterventionVaccination iv WITH iv.id = cv.intervationVaccination
            INNER JOIN App\Entity\Praticien pr WITH pr.id = iv.praticienPrescripteur
            INNER JOIN App\Entity\User u WITH u.id = pr.user
            WHERE u.id = :user AND cv.datePriseInitiale IS NOT NULL
            GROUP BY tv.id
        ')->setParameter('user', $userId);
        return $query->getResult();
    }
// -------------------------------------------------------------------------------------------

// --------------------------------------------------------------------------------------------
// Get statistic of vaccin provided by the current user/praticien
// --------------------------------------------------------------------------------------------
    // SELECT vaccin.vaccin_name, COUNT(vaccin.id)
    // FROM vaccin
    // INNER JOIN carnet_vaccination
    // INNER JOIN intervention_vaccination
    // INNER JOIN praticien
    // INNER JOIN user
    // WHERE carnet_vaccination.vaccin_id = vaccin.id
    // AND intervention_vaccination.id = carnet_vaccination.intervation_vaccination_id
    // AND praticien.id = intervention_vaccination.praticien_prescripteur_id
    // AND user.id = praticien.user_id
    // AND carnet_vaccination.date_prise_initiale IS NOT NULL
    // AND user.id = 2
    // GROUP BY vaccin.id
// --------------------------------------------------------------------------------------------
    public function getVaccStat($userId){
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery("
            SELECT v.vaccinName as label, COUNT(v.id) as y
            FROM App\Entity\Vaccin v
            INNER JOIN App\Entity\CarnetVaccination cv WITH cv.vaccin = v.id
            INNER JOIN App\Entity\InterventionVaccination iv WITH iv.id = cv.intervationVaccination
            INNER JOIN App\Entity\Praticien pr WITH pr.id = iv.praticienPrescripteur
            INNER JOIN App\Entity\User u WITH u.id = pr.user
            WHERE u.id = :userId
            AND cv.datePriseInitiale IS NOT NULL
            GROUP BY v.id
        ")->setParameter('userId', $userId);

        return $query->getResult();
    }
// --------------------------------------------------------------------------------------------
}

