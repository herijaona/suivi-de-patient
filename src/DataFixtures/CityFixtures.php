<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Region;
use App\Entity\State;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Cameroun
        $state_cameroun = new State();
        $state_cameroun->setNameState('CAMEROUN');
        $manager->persist($state_cameroun);

        $region_ADAMAOUA = new Region();
        $region_ADAMAOUA->setNameRegion('ADAMAOUA');
        $region_ADAMAOUA->setState($state_cameroun);
        $manager->persist($region_ADAMAOUA);

        $city_ngaoundal = new City();
        $city_ngaoundal->setNameCity('NGAOUNDAL');
        $city_ngaoundal->setRegion($region_ADAMAOUA);
        $manager->persist($city_ngaoundal);

        $city_TIBATI = new City();
        $city_TIBATI->setNameCity('TIBATI');
        $city_TIBATI->setRegion($region_ADAMAOUA);
        $manager->persist($city_TIBATI);

        $city_MAYO_BALEO = new City();
        $city_MAYO_BALEO->setNameCity('MAYO-BALEO');
        $city_MAYO_BALEO->setRegion($region_ADAMAOUA);
        $manager->persist($city_MAYO_BALEO);

        $city_TIGNERE = new City();
        $city_TIGNERE->setNameCity('TIGNERE');
        $city_TIGNERE->setRegion($region_ADAMAOUA);
        $manager->persist($city_TIGNERE);

        $city_GALIM_TIGNERE = new City();
        $city_GALIM_TIGNERE->setNameCity('GALIM-TIGNERE');
        $city_GALIM_TIGNERE->setRegion($region_ADAMAOUA);
        $manager->persist($city_GALIM_TIGNERE);

        //france

        $state_france = new State();
        $state_france->setNameState('FRANCE');
        $manager->persist($state_france);

        $region_Auvergne = new Region();
        $region_Auvergne->setNameRegion('Auvergne-Rhône-Alpes');
        $region_Auvergne->setState($state_france);
        $manager->persist($region_Auvergne);

        $city_LYON = new City();
        $city_LYON->setNameCity('LYON');
        $city_LYON->setRegion($region_Auvergne);
        $manager->persist($city_LYON);

        $manager->flush();
    }
}
