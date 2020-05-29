<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Region;
use App\Entity\State;
use App\Entity\TypeVaccin;
use App\Entity\User;
use App\Entity\Vaccin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $passwordEncoder;
    function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        //admin
        $user = new User();
        $user->setFirstName('admin');
        $user->setLastName('admin');
        $user->setEmail('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
               'admin'
            )
        );
        $user->setEtat(1);
        $manager->persist($user);

        //vaccin type
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


        // Cameroun
        $state_cameroun = new State();
        $state_cameroun->setNameState('CAMEROUN');
        $manager->persist($state_cameroun);
                // Cameroun region
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

            // Cameroun vaccin
            $vaccin_bcg = new Vaccin();
            $vaccin_bcg->setState($state_cameroun);
            $vaccin_bcg->setTypeVaccin($type_enfant);
            $vaccin_bcg->setVaccinName("Antituberculeux : B.C.G");
            $vaccin_bcg->setVaccinDescription('VPO - 0');
            $manager->persist($vaccin_bcg);

            $vaccin_dtc = new Vaccin();
            $vaccin_dtc->setState($state_cameroun);
            $vaccin_dtc->setTypeVaccin($type_enfant);
            $vaccin_dtc->setVaccinName("DTC – HepB + Hib 1");
            $vaccin_dtc->setVaccinDescription("DOSE 1");
            $manager->persist($vaccin_dtc);

            $vaccin_dtc_hep_hib2 = new Vaccin();
            $vaccin_dtc_hep_hib2->setState($state_cameroun);
            $vaccin_dtc_hep_hib2->setTypeVaccin($type_enfant);
            $vaccin_dtc_hep_hib2->setVaccinName("DTC-HepB2 + Hib2");
            $vaccin_dtc_hep_hib2->setVaccinDescription("DOSE 2");
            $manager->persist($vaccin_dtc_hep_hib2);

            $vaccin_dtc_hep_hib3 = new Vaccin();
            $vaccin_dtc_hep_hib3->setState($state_cameroun);
            $vaccin_dtc_hep_hib3->setTypeVaccin($type_enfant);
            $vaccin_dtc_hep_hib3->setVaccinName("DTC-HepB2 + Hib3");
            $vaccin_dtc_hep_hib2->setVaccinDescription("DOSE 3");
            $manager->persist($vaccin_dtc_hep_hib3);

            $vaccin_pneumo1 = new Vaccin();
            $vaccin_pneumo1->setState($state_cameroun);
            $vaccin_pneumo1->setTypeVaccin($type_enfant);
            $vaccin_pneumo1->setVaccinName("Pneumo 13-1 (VPO-1 + Rota1)");
            $manager->persist($vaccin_pneumo1);

            $vaccin_pneumo2 = new Vaccin();
            $vaccin_pneumo2->setState($state_cameroun);
            $vaccin_pneumo2->setTypeVaccin($type_enfant);
            $vaccin_pneumo2->setVaccinName("Pneumo 13-2 (VPO-2 + Rota2)");
            $manager->persist($vaccin_pneumo2);

            $vaccin_pneumo3 = new Vaccin();
            $vaccin_pneumo3->setState($state_cameroun);
            $vaccin_pneumo3->setTypeVaccin($type_enfant);
            $vaccin_pneumo3->setVaccinName("Pneumo 13-3 (VPO-3)");
            $manager->persist($vaccin_pneumo3);

            $vaccin_var = new Vaccin();
            $vaccin_var->setState($state_cameroun);
            $vaccin_var->setTypeVaccin($type_enfant);
            $vaccin_var->setVaccinName("VAR + VAA");
            $manager->persist($vaccin_var);

            $vaccin_ad = new Vaccin();
            $vaccin_ad->setState($state_cameroun);
            $vaccin_ad->setTypeVaccin($type_ADULTE);
            $vaccin_ad->setVaccinName("Calendrier Vaccin Adulte");
            $vaccin_ad->setVaccinDescription('Rappel dTcaP1 ou dTP si dernier');
            $manager->persist($vaccin_ad);

            $vaccin_ad_ca = new Vaccin();
            $vaccin_ad_ca->setState($state_cameroun);
            $vaccin_ad_ca->setTypeVaccin($type_ADULTE);
            $vaccin_ad_ca->setVaccinName("Coqueluche acellulaire (ca)");
            $vaccin_ad_ca->setVaccinDescription("rappel de dTcaP < 5 ans");
            $manager->persist($vaccin_ad_ca);

            $vaccin_gripe = new Vaccin();
            $vaccin_gripe->setState($state_cameroun);
            $vaccin_gripe->setTypeVaccin($type_AGE3);
            $vaccin_gripe->setVaccinName("Grippe");
            $vaccin_gripe->setVaccinDescription("1 dose annuelle dès 65 ans");
            $manager->persist($vaccin_gripe);

            $vaccin_zona = new Vaccin();
            $vaccin_zona->setState($state_cameroun);
            $vaccin_zona->setTypeVaccin($type_AGE3);
            $vaccin_zona->setVaccinName("Zona");
            $vaccin_zona->setVaccinDescription("une dose");
            $manager->persist($vaccin_zona);

            $vaccin_vat1 = new Vaccin();
            $vaccin_vat1->setState($state_cameroun);
            $vaccin_vat1->setTypeVaccin($type_FEMME_ENCEINTE);
            $vaccin_vat1->setVaccinName("VAT1");
            $vaccin_vat1->setVaccinDescription("Dès le début de la grossesse");
            $manager->persist($vaccin_vat1);

            $vaccin_vat2 = new Vaccin();
            $vaccin_vat2->setState($state_cameroun);
            $vaccin_vat2->setTypeVaccin($type_FEMME_ENCEINTE);
            $vaccin_vat2->setVaccinName("VAT2");
            $vaccin_vat2->setVaccinDescription("1 mois au moins après VAT1");
            $manager->persist($vaccin_vat2);

            $vaccin_vat3 = new Vaccin();
            $vaccin_vat3->setState($state_cameroun);
            $vaccin_vat3->setTypeVaccin($type_FEMME_ENCEINTE);
            $vaccin_vat3->setVaccinName("VAT3");
            $vaccin_vat3->setVaccinDescription("six mois après VAT2");
            $manager->persist($vaccin_vat3);

            $vaccin_vat4 = new Vaccin();
            $vaccin_vat4->setState($state_cameroun);
            $vaccin_vat4->setTypeVaccin($type_FEMME_ENCEINTE);
            $vaccin_vat4->setVaccinName("VAT4");
            $vaccin_vat4->setVaccinDescription("1 an après VAT3");
            $manager->persist($vaccin_vat4);

            $vaccin_vat5 = new Vaccin();
            $vaccin_vat5->setState($state_cameroun);
            $vaccin_vat5->setTypeVaccin($type_FEMME_ENCEINTE);
            $vaccin_vat5->setVaccinName("VAT5");
            $vaccin_vat5->setVaccinDescription("1 an après VAT4");
            $manager->persist($vaccin_vat5);

        //france

        $state_france = new State();
        $state_france->setNameState('FRANCE');
        $manager->persist($state_france);
            // france region
            $region_Auvergne = new Region();
            $region_Auvergne->setNameRegion('Auvergne-Rhône-Alpes');
            $region_Auvergne->setState($state_france);
            $manager->persist($region_Auvergne);

                $city_LYON = new City();
                $city_LYON->setNameCity('LYON');
                $city_LYON->setRegion($region_Auvergne);
                $manager->persist($city_LYON);



            // france vaccin
            $vaccin_dtcap = new Vaccin();
            $vaccin_dtcap->setState($state_france);
            $vaccin_dtcap->setTypeVaccin($type_enfant);
            $vaccin_dtcap->setVaccinName("DTCaP");
            $vaccin_dtcap->setVaccinDescription("Diphtérie (D), Tétanos (T), coqueluche acellulaire (Ca), Poliomyélite (P)");
            $manager->persist($vaccin_dtcap);

            $vaccin_hib = new Vaccin();
            $vaccin_hib->setState($state_france);
            $vaccin_hib->setTypeVaccin($type_enfant);
            $vaccin_hib->setVaccinName("Hib");
            $vaccin_hib->setVaccinDescription("HibHaemophilus influenzae b (Hib)");
            $manager->persist($vaccin_hib);

            $vaccin_hibb = new Vaccin();
            $vaccin_hibb->setState($state_france);
            $vaccin_hibb->setTypeVaccin($type_enfant);
            $vaccin_hibb->setVaccinName("Hep B");
            $vaccin_hibb->setVaccinDescription("Hépatite B (Hep B)");
            $manager->persist($vaccin_hibb);

            $vaccin_pnc = new Vaccin();
            $vaccin_pnc->setState($state_france);
            $vaccin_pnc->setTypeVaccin($type_enfant);
            $vaccin_pnc->setVaccinName("PnC");
            $vaccin_pnc->setVaccinDescription("Pneumocoque (PnC)1");
            $manager->persist($vaccin_pnc);

            $vaccin_mnc = new Vaccin();
            $vaccin_mnc->setState($state_france);
            $vaccin_mnc->setTypeVaccin($type_enfant);
            $vaccin_mnc->setVaccinName("MnC");
            $vaccin_mnc->setVaccinDescription("Méningocoque C (vaccin conjugué MnC)");
            $manager->persist($vaccin_mnc);

            $vaccin_ror = new Vaccin();
            $vaccin_ror->setState($state_france);
            $vaccin_ror->setTypeVaccin($type_enfant);
            $vaccin_ror->setVaccinName("ROR");
            $vaccin_ror->setVaccinDescription("Rougeole (R), Oreillons (O), Rubéole ®");
            $manager->persist($vaccin_ror);

            $vaccin_dtcap_ado = new Vaccin();
            $vaccin_dtcap_ado->setState($state_france);
            $vaccin_dtcap_ado->setTypeVaccin($type_enfant);
            $vaccin_dtcap_ado->setVaccinName("dTcaP-ado");
            $vaccin_dtcap_ado->setVaccinDescription("diphtérie (d), Tétanos (T), coqueluche acellulaire (ca), Poliomyélite (P)2");
            $manager->persist($vaccin_dtcap_ado);

            $vaccin_hpv = new Vaccin();
            $vaccin_hpv->setState($state_france);
            $vaccin_hpv->setTypeVaccin($type_enfant);
            $vaccin_hpv->setVaccinName("HPV");
            $vaccin_hpv->setVaccinDescription("Papillomavirus humains (HPV) chez les jeunes filles jeunes garçons");
            $manager->persist($vaccin_hpv);

            $vaccin_bcg = new Vaccin();
            $vaccin_bcg->setState($state_france);
            $vaccin_bcg->setTypeVaccin($type_enfant);
            $vaccin_bcg->setVaccinName("BCG");
            $vaccin_bcg->setVaccinDescription("Tuberculose (BCG)");
            $manager->persist($vaccin_bcg);


        $manager->flush();
    }
}
