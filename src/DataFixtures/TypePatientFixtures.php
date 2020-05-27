<?php

namespace App\DataFixtures;

use App\Entity\TypePatient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypePatientFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $type_ADULTE = new TypePatient();
        $type_ADULTE->setTypePatientName('ADULTE');
        $manager->persist($type_ADULTE);

        $type_FEMMEENCEINTE = new TypePatient();
        $type_FEMMEENCEINTE->setTypePatientName('FEMME ENCEINTE');
        $manager->persist($type_FEMMEENCEINTE);
        $type_ENFANT = new TypePatient();

        $type_ENFANT->setTypePatientName('ENFANT');
        $manager->persist($type_ENFANT);
        $manager->flush();
    }
}
