<?php

namespace App\DataFixtures;

use App\Entity\CentreType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CentreTypeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $centre_hg = new CentreType();
        $centre_hg->setTypeName('HG');
        $centre_hg->setDescription('Hôpital Général');
        $manager->persist($centre_hg);

        $centre_CHU = new CentreType();
        $centre_CHU->setTypeName('CHU');
        $centre_CHU->setDescription('Centre Hospitalier Universitaire');
        $manager->persist($centre_CHU);

        $centre_hc = new CentreType();
        $centre_hc->setTypeName('HC');
        $centre_hc->setDescription('Hôpital Central');
        $manager->persist($centre_hc);

        $centre_cl = new CentreType();
        $centre_cl->setTypeName('CL');
        $centre_cl->setDescription('Clinique');
        $manager->persist($centre_cl);

        $centre_cp = new CentreType();
        $centre_cp->setTypeName('CP');
        $centre_cp->setDescription('Cabinet Privé');
        $manager->persist($centre_cp);

        $centre_fsc = new CentreType();
        $centre_fsc->setTypeName('FSC');
        $centre_fsc->setDescription('Formation sanitaire confessionnelle');
        $manager->persist($centre_fsc);

        $centre_di = new CentreType();
        $centre_di->setTypeName('DI');
        $centre_di->setDescription('Dispensaire');
        $manager->persist($centre_di);

        $centre_hep = new CentreType();
        $centre_hep->setTypeName('HEPr');
        $centre_hep->setDescription('Hôpital d\'Entreprise privée');
        $manager->persist($centre_hep);

        $centre_hep1 = new CentreType();
        $centre_hep1->setTypeName('HEPa');
        $centre_hep1->setDescription('Hôpital d\'Entreprise parapublique');
        $manager->persist($centre_hep1);

        $centre_hd = new CentreType();
        $centre_hd->setTypeName('HD');
        $centre_hd->setDescription('Hôpital de district');
        $manager->persist($centre_hd);

        $centre_ssi = new CentreType();
        $centre_ssi->setTypeName('SSI');
        $centre_ssi->setDescription('Service de santé de district');
        $manager->persist($centre_ssi);

        $centre_cma = new CentreType();
        $centre_cma->setTypeName('CMA');
        $centre_cma->setDescription('Centre Médical d\'arrondissement');
        $manager->persist($centre_cma);

        $centre_csi = new CentreType();
        $centre_csi->setTypeName('CSI');
        $centre_csi->setDescription('Centre de Santé Intégré');
        $manager->persist($centre_csi);

        $centre_cs = new CentreType();
        $centre_cs->setTypeName('CS');
        $centre_cs->setDescription('Centre de Santé');
        $manager->persist($centre_cs);

        $centre_csa = new CentreType();
        $centre_csa->setTypeName('CSA');
        $centre_csa->setDescription('Centre de Santé Ambulatoire');
        $manager->persist($centre_csa);

        $manager->flush();
    }
}
