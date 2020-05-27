<?php

namespace App\DataFixtures;

use App\Entity\TypeVaccin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeVaccinFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $type_enfant = new TypeVaccin();
        $type_enfant->setTypeName('ENFANT');
        $manager->persist($type_enfant);

        $type_ADULTE = new TypeVaccin();
        $type_ADULTE->setTypeName('ADULTE');
        $manager->persist($type_ADULTE);

        $type_AGE3 = new TypeVaccin();
        $type_AGE3->setTypeName('AGE3');
        $manager->persist($type_AGE3);

        $type_FEMME_ENCEINTE = new TypeVaccin();
        $type_FEMME_ENCEINTE->setTypeName('FEMME ENCEINTE');
        $manager->persist($type_FEMME_ENCEINTE);
        $manager->flush();
    }
}
