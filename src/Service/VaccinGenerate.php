<?php


namespace App\Service;


use App\Entity\CarnetVaccination;
use App\Entity\OrdoVaccination;
use App\Repository\InterventionVaccinationRepository;
use App\Repository\VaccinRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

class VaccinGenerate
{
    protected $entityManager;
    protected $vaccinRepository;
    protected $interventionVaccinationRepository;

    function __construct(
        EntityManagerInterface $entityManager,
        VaccinRepository $vaccinRepository,
        InterventionVaccinationRepository $interventionVaccinationRepository)
    {
        $this->entityManager = $entityManager;
        $this->vaccinRepository = $vaccinRepository;
        $this->interventionVaccinationRepository = $interventionVaccinationRepository;
    }


    public function generateCalendar($patient, $dateNow)
    {
        $type_patient = $patient->getTypePatient()->getTypePatientName();
        $state = $patient->getAddressOnBorn()->getRegion()->getState()->getNameState();
        $birthday = $patient->getDateOnBorn();
        $listVaccin = [];
        $day_preg =  $dateNow;
        if($state == 'FRANCE'){
            switch ($type_patient){
                case 'ENFANT':
                    $alls = $this->vaccinRepository->findVaccinByTYpe('ENFANT', $state);
                    $listVaccin = $this->generate_vaccin($patient, $birthday, $alls);
                    break;
                case 'ADULTE':
                    $alls = $this->vaccinRepository->findVaccinByTYpe('ADULTE');
                    $listVaccin = $this->generate_vaccin($patient, $birthday, $alls);
                    break;
                case 'FEMME ENCEINTE':
                    $alls = $this->vaccinRepository->findVaccinByTYpe('FEMME ENCEINTE');
                    $listVaccin = $this->generate_vaccin($patient, $birthday, $alls);
                    break;
            }

        }else{
            switch ($type_patient){
                case 'ENFANT':
                    $alls = $this->vaccinRepository->findVaccinByTYpe('ENFANT', $state);
                    $listVaccin = $this->generate_vaccin($patient, $birthday, $alls);
                    break;
                case 'ADULTE':
                    $alls = $this->vaccinRepository->findVaccinByTYpe('ADULTE');
                    $listVaccin = $this->generate_vaccin($patient, $birthday, $alls);
                    break;
                case 'FEMME ENCEINTE':
                    $alls = $this->vaccinRepository->findVaccinByTYpe('FEMME ENCEINTE');
                    $listVaccin = $this->generate_vaccin($patient, $birthday, $alls);
                    break;
            }

        }
        return $listVaccin;
    }

    /**
     * @param $patient
     * @param $birthday
     * @param $vaccinAll
     * @param $IntervationVaccination
     */
    public function generate_vaccin($patient, $birthday, $vaccinAll)
    {
        $listVaccin = [];
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        foreach ( $vaccinAll as $vacc){
            if($vacc != null){
                $datePriseInitiale = $vacc->getDatePriseInitiale();
                $rappel1 = $vacc->getRappel1();
                $rappel2 = $vacc->getRappel2();
                $rappel3 = $vacc->getRappel3();
                $rappel4 = $vacc->getRappel4();
                $rappel5 = $vacc->getRappel5();
                $rappel6 = $vacc->getRappel6();
                $type_ins = 'week';
                $diffIn = 1;
                if($datePriseInitiale != null){
                    $type_ins = explode(" ", $datePriseInitiale)[1];
                }
                elseif ($rappel1 != null){
                    $type_ins = explode(" ", $rappel1)[1];
                }elseif ($rappel2 != null){
                    $type_ins = explode(" ", $rappel2)[1];
                }elseif ( $rappel3 != null){
                    $type_ins = explode(" ", $rappel3)[1];
                }elseif ( $rappel4 != null){
                    $type_ins = explode(" ", $rappel4)[1];
                }elseif ( $rappel5 != null){
                    $type_ins = explode(" ", $rappel5)[1];
                }elseif ( $rappel6 != null){
                    $type_ins = explode(" ", $rappel6)[1];
                }

                for ($j = 0; $j <= 10; $j++){

                    if ($j == 0){
                        $getVAcc = $datePriseInitiale;
                    }else{
                        $getVAcc = $vacc->getRappel.$j.'()';
                    }


                    $intervation = $this->interventionVaccinationRepository->findOneBy(['patient'=>$patient, 'vaccin' => $vacc]);

                    $crnV = new CarnetVaccination();
                    $crnV->setPatient($patient);
                    $crnV->setVaccin($vacc);
                    $crnV->setEtat(false);
                    $crnV->setIntervationVaccination($intervation);

                    switch ($type_ins){
                        case 'week':
                            $diffIn = $birth->diffInWeeks($date_now);
                            break;
                        case 'month':
                            $diffIn = $birth->diffInMonths($date_now);
                            break;
                        case 'year':
                            $diffIn = $birth->diffInYears($date_now);
                            break;
                        default:
                            break;
                    }

                    if ($getVAcc != null){
                        if($diffIn < intval(explode(" ", $getVAcc)[1])) {
                            switch ($type_ins){
                                case 'week':
                                    $date = $this->add_rdv_in_week($diffIn,$birthday);
                                    break;
                                case 'month':
                                    $date = $this->add_rdv_in_month($diffIn,$birthday);
                                    break;
                                case 'year':
                                    $date = $this->add_rdv_in_year($diffIn,$birthday);
                                    break;
                            }
                            $crnV->setDatePriseInitiale($date);
                            //$crnV->setRappelVaccin($date);

                        }elseif ($diffIn == intval(explode(" ", $getVAcc)[1])){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $crnV->setDatePriseInitiale($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $crnV->setDatePriseInitiale($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $crnV->setDatePriseInitiale($date);
                            }
                        }

                        /*if ($j+1 <= 10){
                            if ($vacc->getRappel. $j+1 .'()' != null)
                                $dateR = $this->add_rdv_in_week($diffIn,$birthday);
                        }*/
                        $this->entityManager->persist($crnV);
                        $this->entityManager->flush();
                        array_push($listVaccin, $crnV);
                    }
                }
            }
        }
        return $listVaccin;
    }

    /**
     * generate vaccin Antituberculeux : B.C.G,DTC – HepB + Hib 1, for child cameroun
     * @param $patient
     * @param $birthday
     * @param $etat
     * @param $praticien
     */
    public function ca_vaccin_enfant($patient,$birthday,$vaccinName,$_weeks,$etat,$praticien)
    {
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $week = $birth->diffInWeeks($date_now);
        $vacc = $this->vaccinRepository->findOneBy(['vaccinName'=>$vaccinName]);


        if($vacc){
            if ($week <= $_weeks){

                $rdv = new OrdoVaccination();
                $rdv->setPatient($patient);
                $rdv->setVaccin($vacc);
                $rdv->setReferencePraticienExecutant($praticien);

                //$rdv->setOrdonnance($ordonance);
                $rdv->setEtat($etat);
                if($week==$_weeks){
                    if(!$date_now->isWeekend()){
                        $tomorrow = $date_now->addDay();
                        if($tomorrow->day != 6){
                            $rdv->setDatePrise($tomorrow);
                        }else{
                            $monday = $date_now->addDays(3);
                            $rdv->setDatePrise($monday);
                        }
                    }else{
                        $date = $date_now->addDays(2);
                        $rdv->setDatePrise($date);
                    }
                }else{
                    $week_ = $this->add_rdv_in_week($_weeks,$birthday);
                    $rdv->setDatePrise($week_);
                }
                $rdv->setStatusVaccin(0);

                $this->entityManager->persist($rdv);
                $this->entityManager->flush();
            }
        }
    }

    /**
     * generate vaccin Calendrier Vaccin Adulte for parent cameroun
     * @param $patient
     * @param $birthday
     * @param $etat
     * @param $praticien
     */
    public function ca_vaccin_adulte($patient,$birthday,$etat,$praticien)
    {
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $year = $birth->diffInYears($date_now);

        $vaccin = $this->vaccinRepository->findOneBy(['vaccin_name'=>'Calendrier Vaccin Adulte']);
        if ($vaccin){
            if($year <= 25){
                for ($i=1; $i<=7; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 25){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $rdv->setDateRdv($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $rdv->setDateRdv($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $rdv->setDateRdv($date);// 4mois
                            }
                        }else{
                            $year_25 = $this->add_rdv_in_year(25,$birthday);
                            $rdv->setDateRdv($year_25);
                        }
                    }elseif ($i==2){
                        $year_45 = $this->add_rdv_in_year(45,$birthday);
                        $rdv->setDateRdv($year_45);
                    }elseif ($i==3){
                        $year_65 = $this->add_rdv_in_year(65,$birthday);
                        $rdv->setDateRdv($year_65);
                    }elseif ($i==4){
                        $year_75 = $this->add_rdv_in_year(75,$birthday);
                        $rdv->setDateRdv($year_75);
                    }elseif ($i==5){
                        $year_85 = $this->add_rdv_in_year(85,$birthday);
                        $rdv->setDateRdv($year_85);
                    }elseif ($i==6){
                        $year_95 = $this->add_rdv_in_year(95,$birthday);
                        $rdv->setDateRdv($year_95);
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDateRdv($year_105);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }

            }elseif($year<=45){
                for ($i=1; $i<=6; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 45){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $rdv->setDateRdv($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $rdv->setDateRdv($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $rdv->setDateRdv($date);// 4mois
                            }
                        }else{
                            $year_45 = $this->add_rdv_in_year(45,$birthday);
                            $rdv->setDateRdv($year_45);
                        }
                    }elseif ($i==2){
                        $year_65 = $this->add_rdv_in_year(65,$birthday);
                        $rdv->setDateRdv($year_65);
                    }elseif ($i==3){
                        $year_75 = $this->add_rdv_in_year(75,$birthday);
                        $rdv->setDateRdv($year_75);
                    }elseif ($i==4){
                        $year_85 = $this->add_rdv_in_year(85,$birthday);
                        $rdv->setDateRdv($year_85);
                    }elseif ($i==5){
                        $year_95 = $this->add_rdv_in_year(95,$birthday);
                        $rdv->setDateRdv($year_95);
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDateRdv($year_105);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($year<=65){
                for ($i=1; $i<=5; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 65){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $rdv->setDateRdv($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $rdv->setDateRdv($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $rdv->setDateRdv($date);// 4mois
                            }
                        }else{
                            $year_65 = $this->add_rdv_in_year(65,$birthday);
                            $rdv->setDateRdv($year_65);
                        }
                    }elseif ($i==2){
                        $year_75 = $this->add_rdv_in_year(75,$birthday);
                        $rdv->setDateRdv($year_75);
                    }elseif ($i==3){
                        $year_85 = $this->add_rdv_in_year(85,$birthday);
                        $rdv->setDateRdv($year_85);
                    }elseif ($i==4){
                        $year_95 = $this->add_rdv_in_year(95,$birthday);
                        $rdv->setDateRdv($year_95);
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDateRdv($year_105);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($year<=75){
                for ($i=1; $i<=4; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 75){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $rdv->setDateRdv($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $rdv->setDateRdv($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $rdv->setDateRdv($date);// 4mois
                            }
                        }else{
                            $year_75 = $this->add_rdv_in_year(75,$birthday);
                            $rdv->setDateRdv($year_75);
                        }
                    }elseif ($i==2){
                        $year_85 = $this->add_rdv_in_year(85,$birthday);
                        $rdv->setDateRdv($year_85);
                    }elseif ($i==3){
                        $year_95 = $this->add_rdv_in_year(95,$birthday);
                        $rdv->setDateRdv($year_95);
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDateRdv($year_105);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($year<=85){
                for ($i=1; $i<=3; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 85){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $rdv->setDateRdv($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $rdv->setDateRdv($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $rdv->setDateRdv($date);// 4mois
                            }
                        }else{
                            $year_85 = $this->add_rdv_in_year(85,$birthday);
                            $rdv->setDateRdv($year_85);
                        }
                    }elseif ($i==2){
                        $year_95 = $this->add_rdv_in_year(95,$birthday);
                        $rdv->setDateRdv($year_95);
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDateRdv($year_105);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($year<=95){
                for ($i=1; $i<=2; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 95){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $rdv->setDateRdv($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $rdv->setDateRdv($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $rdv->setDateRdv($date);// 4mois
                            }
                        }else{
                            $year_95 = $this->add_rdv_in_year(95,$birthday);
                            $rdv->setDateRdv($year_95);
                        }
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDateRdv($year_105);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($year<=105){
                $rdv = new RendezVous();
                $rdv->setPatient($patient);
                $rdv->setVaccin($vaccin);
                $rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($year == 105){
                    if(!$date_now->isWeekend()){
                        $tomorrow = $date_now->addDay();
                        if($tomorrow->day != 6){
                            $rdv->setDateRdv($tomorrow);
                        }else{
                            $monday = $date_now->addDays(3);
                            $rdv->setDateRdv($monday);
                        }
                    }else{
                        $date = $date_now->addDays(2);
                        $rdv->setDateRdv($date);// 4mois
                    }
                }else{
                    $year_105 = $this->add_rdv_in_year(105,$birthday);
                    $rdv->setDateRdv($year_105);
                }
                $rdv->setStatus(false);
                $this->entityManager->persist($rdv);

            }
        }
    }

    /**
     * generate vaccin Coqueluche acellulaire (ca),Zona for parent cameroun
     * @param $patient
     * @param $birthday
     * @param $vaccin_name
     * @param $_year
     * @param $etat
     * @param $praticien
     */
    public function ca_vaccin_adult_age3($patient,$birthday,$vaccin_name,$_year,$etat,$praticien)
    {
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $year = $birth->diffInYears($date_now);

        $vaccin = $this->vaccinRepository->findOneBy(['vaccin_name'=>$vaccin_name]);
        if ($vaccin){
            if($year <= $_year){
                $rdv = new RendezVous();
                $rdv->setPatient($patient);
                $rdv->setVaccin($vaccin);
                $rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($year == $_year){
                    if(!$date_now->isWeekend()){
                        $tomorrow = $date_now->addDay();
                        if($tomorrow->day != 6){
                            $rdv->setDateRdv($tomorrow);
                        }else{
                            $monday = $date_now->addDays(3);
                            $rdv->setDateRdv($monday);
                        }
                    }else{
                        $date = $date_now->addDays(2);
                        $rdv->setDateRdv($date);// 4mois
                    }
                }else{
                    $year_ = $this->add_rdv_in_year($_year,$birthday);
                    $rdv->setDateRdv($year_);
                }
                $rdv->setStatus(false);
                $this->entityManager->persist($rdv);
            }
        }
    }

    /**
     * generate vaccin gripe for parent cameroun
     * @param $patient
     * @param $birthday
     * @param $etat
     * @param $praticien
     */
    public function ca_grippe($patient,$birthday,$etat,$praticien)
    {
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $year = $birth->diffInYears($date_now);

        $vaccin = $this->vaccinRepository->findOneBy(['vaccin_name'=>'Grippe']);
        if ($vaccin){
            if($year >= 65){
                for ($year;$year<=105;$year++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    $year_ = $this->add_rdv_in_year($year,$birthday);
                    $rdv->setDateRdv($year_);
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
                $this->entityManager->flush();
            }

        }
    }

    /**
     * generate vaccin VAT for parent enceinte
     * @param $patient
     * @param $day_preg
     * @param $vaccin_name
     * @param $_month
     * @param $etat
     * @param $praticien
     * @param null $type
     */
    public function ca_enceinte($patient,$day_preg,$vaccin_name,$_month,$etat,$praticien,$type=null)
    {
        $preg = Carbon::parse($day_preg);
        $date_now = Carbon::now();
        $month = $preg->diffInMonths($date_now);

        $vaccin = $this->vaccinRepository->findOneBy(['vaccin_name'=>$vaccin_name]);
        if ($vaccin){
            if(!$type){
                $rdv = new RendezVous();
                $rdv->setPatient($patient);
                $rdv->setVaccin($vaccin);
                $rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                $rdv->setStatus(false);
                $month_ = $this->add_rdv_in_month($_month,$preg);
                $rdv->setDateRdv($month_);
                $this->entityManager->persist($rdv);
            }else{
                $rdv = new RendezVous();
                $rdv->setPatient($patient);
                $rdv->setVaccin($vaccin);
                $rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                $rdv->setDateRdv($preg);
                $rdv->setStatus(false);
                $this->entityManager->persist($rdv);
            }
        }
    }
    /**
     * generate vaccin DTCap for child france
     * @param $patient
     * @param $birthday
     * @param $etat
     * @param $praticien
     */
    private function fr_DTCaP($patient,$birthday,$etat,$praticien){
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $month = $birth->diffInMonths($date_now);

        $dtcap = $this->vaccinRepository->findOneBy(['vaccin_name'=>'DTCaP']);
        if ($dtcap){
            if($month <= 4){
                for ($i=1; $i<=4; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($dtcap);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($month == 4){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $rdv->setDateRdv($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $rdv->setDateRdv($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $rdv->setDateRdv($date);// 4mois
                            }
                        }else{
                            $month_4 = $this->add_rdv_in_month(4,$birthday);
                            $rdv->setDateRdv($month_4);
                        }
                    }elseif ($i==2){
                        $month_5 = $this->add_rdv_in_month(5,$birthday);
                        $rdv->setDateRdv($month_5);
                    }elseif ($i==3){
                        $month_12 = $this->add_rdv_in_month(12,$birthday);
                        $rdv->setDateRdv($month_12);
                    }else{
                        $month_132 = $this->add_rdv_in_month(132,$birthday);
                        $rdv->setDateRdv($month_132);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month == 5){
                for ($i=1; $i<=3; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($dtcap);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if(!$date_now->isWeekend()){
                            $tomorrow = $date_now->addDay();
                            if($tomorrow->day != 6){
                                $rdv->setDateRdv($tomorrow);
                            }else{
                                $monday = $date_now->addDays(3);
                                $rdv->setDateRdv($monday);
                            }
                        }else{
                            $date = $date_now->addDays(2);
                            $rdv->setDateRdv($date);// 5mois
                        }
                    }elseif ($i==2){
                        $month_12 = $this->add_rdv_in_month(12,$birthday);
                        $rdv->setDateRdv($month_12);
                    }else{
                        $month_132 = $this->add_rdv_in_month(132,$birthday);
                        $rdv->setDateRdv($month_132);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month <= 12){
                for ($i=1; $i<=2; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($dtcap);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($month == 12){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $rdv->setDateRdv($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $rdv->setDateRdv($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $rdv->setDateRdv($date);// 12mois
                            }
                        }else{
                            $month_12 = $this->add_rdv_in_month(12,$birthday);
                            $rdv->setDateRdv($month_12);
                        }
                    }else{
                        $month_132 = $this->add_rdv_in_month(132,$birthday);
                        $rdv->setDateRdv($month_132);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month <= 132){
                $rdv = new RendezVous();
                $rdv->setPatient($patient);
                $rdv->setVaccin($dtcap);
                $rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($month == 132){
                    if(!$date_now->isWeekend()){
                        $tomorrow = $date_now->addDay();
                        if($tomorrow->day != 6){
                            $rdv->setDateRdv($tomorrow);
                        }else{
                            $monday = $date_now->addDays(3);
                            $rdv->setDateRdv($monday);
                        }
                    }else{
                        $date = $date_now->addDays(2);
                        $rdv->setDateRdv($date);// 132mois
                    }
                }else{
                    $month_132 = $this->add_rdv_in_month(132,$birthday);
                    $rdv->setDateRdv($month_132);
                }
                $rdv->setStatus(false);
                $this->entityManager->persist($rdv);
            }
            $this->entityManager->flush();
        }

    }

    /**
     * generate vaccin Hib, Hep B Pnc for child france
     * @param $patient
     * @param $birthday
     * @param $vaccin_name
     * @param $etat
     * @param $praticien
     */
    private function fr_Hib_hepb_pnc($patient,$birthday,$vaccin_name,$etat,$praticien){
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $month = $birth->diffInMonths($date_now);

        $Hib = $this->vaccinRepository->findOneBy(['vaccin_name'=>$vaccin_name]);
        if ($Hib){
            if($month <= 4){
                for ($i=1; $i<=3; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($Hib);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($month == 4){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $rdv->setDateRdv($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $rdv->setDateRdv($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $rdv->setDateRdv($date);// 4mois
                            }
                        }else{
                            $month_4 = $this->add_rdv_in_month(4,$birthday);
                            $rdv->setDateRdv($month_4);
                        }
                    }elseif ($i==2){
                        $month_5 = $this->add_rdv_in_month(5,$birthday);
                        $rdv->setDateRdv($month_5);
                    }else{
                        $month_12 = $this->add_rdv_in_month(12,$birthday);
                        $rdv->setDateRdv($month_12);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month == 5){
                for ($i=1; $i<=2; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($Hib);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if(!$date_now->isWeekend()){
                            $tomorrow = $date_now->addDay();
                            if($tomorrow->day != 6){
                                $rdv->setDateRdv($tomorrow);
                            }else{
                                $monday = $date_now->addDays(3);
                                $rdv->setDateRdv($monday);
                            }
                        }else{
                            $date = $date_now->addDays(2);
                            $rdv->setDateRdv($date);// 5mois
                        }
                    }else{
                        $month_12 = $this->add_rdv_in_month(12,$birthday);
                        $rdv->setDateRdv($month_12);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month <= 12){
                $rdv = new RendezVous();
                $rdv->setPatient($patient);
                $rdv->setVaccin($Hib);
                $rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($month == 12){
                    if(!$date_now->isWeekend()){
                        $tomorrow = $date_now->addDay();
                        if($tomorrow->day != 6){
                            $rdv->setDateRdv($tomorrow);
                        }else{
                            $monday = $date_now->addDays(3);
                            $rdv->setDateRdv($monday);
                        }
                    }else{
                        $date = $date_now->addDays(2);
                        $rdv->setDateRdv($date);// 12mois
                    }
                }else{
                    $month_12 = $this->add_rdv_in_month(12,$birthday);
                    $rdv->setDateRdv($month_12);
                }
                $rdv->setStatus(false);
                $this->entityManager->persist($rdv);

            }
            $this->entityManager->flush();
        }
    }

    /**
     * generate vaccin MnC for child france
     * @param $patient
     * @param $birthday
     * @param $etat
     * @param $praticien
     */
    private function fr_MnC($patient,$birthday,$etat,$praticien){
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $month = $birth->diffInMonths($date_now);

        $Hib = $this->vaccinRepository->findOneBy(['vaccin_name'=>"MnC"]);
        if ($Hib){
            if($month <= 11){
                for ($i=1; $i<=2; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($Hib);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($month == 11){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $rdv->setDateRdv($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $rdv->setDateRdv($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $rdv->setDateRdv($date);// 11mois
                            }
                        }else{
                            $month_11 = $this->add_rdv_in_month(11,$birthday);
                            $rdv->setDateRdv($month_11);
                        }
                    }else{
                        $month_16 = $this->add_rdv_in_month(16,$birthday);
                        $rdv->setDateRdv($month_16);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month <= 16){
                $rdv = new RendezVous();
                $rdv->setPatient($patient);
                $rdv->setVaccin($Hib);
                $rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($month == 16){
                    if(!$date_now->isWeekend()){
                        $tomorrow = $date_now->addDay();
                        if($tomorrow->day != 6){
                            $rdv->setDateRdv($tomorrow);
                        }else{
                            $monday = $date_now->addDays(3);
                            $rdv->setDateRdv($monday);
                        }
                    }else{
                        $date = $date_now->addDays(2);
                        $rdv->setDateRdv($date);// 16mois
                    }
                }else{
                    $month_16 = $this->add_rdv_in_month(16,$birthday);
                    $rdv->setDateRdv($month_16);
                }
                $rdv->setStatus(false);
                $this->entityManager->persist($rdv);

            }
            $this->entityManager->flush();
        }
    }

    /**
     * generate vaccin ROR for child france
     * @param $patient
     * @param $birthday
     * @param $etat
     * @param $praticien
     */
    private function fr_ROR($patient,$birthday,$etat,$praticien){
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $month = $birth->diffInMonths($date_now);

        $Hib = $this->vaccinRepository->findOneBy(['vaccin_name'=>"ROR"]);
        if ($Hib){
            if($month <= 16){
                for ($i=1; $i<=2; $i++){
                    $rdv = new RendezVous();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($Hib);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($month == 16){
                            if(!$date_now->isWeekend()){
                                $tomorrow = $date_now->addDay();
                                if($tomorrow->day != 6){
                                    $rdv->setDateRdv($tomorrow);
                                }else{
                                    $monday = $date_now->addDays(3);
                                    $rdv->setDateRdv($monday);
                                }
                            }else{
                                $date = $date_now->addDays(2);
                                $rdv->setDateRdv($date);// 16mois
                            }
                        }else{
                            $month_16 = $this->add_rdv_in_month(16,$birthday);
                            $rdv->setDateRdv($month_16);
                        }
                    }else{
                        $month_72 = $this->add_rdv_in_month(72,$birthday);
                        $rdv->setDateRdv($month_72);
                    }
                    $rdv->setStatus(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month <= 72){
                $rdv = new RendezVous();
                $rdv->setPatient($patient);
                $rdv->setVaccin($Hib);
                $rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($month == 72){
                    if(!$date_now->isWeekend()){
                        $tomorrow = $date_now->addDay();
                        if($tomorrow->day != 6){
                            $rdv->setDateRdv($tomorrow);
                        }else{
                            $monday = $date_now->addDays(3);
                            $rdv->setDateRdv($monday);
                        }
                    }else{
                        $date = $date_now->addDays(2);
                        $rdv->setDateRdv($date);// 16mois
                    }
                }else{
                    $month_72 = $this->add_rdv_in_month(72,$birthday);
                    $rdv->setDateRdv($month_72);
                }
                $rdv->setStatus(false);
                $this->entityManager->persist($rdv);

            }
            $this->entityManager->flush();
        }
    }

    /**
     * generate vaccin dTcaP-ado for child france
     * @param $patient
     * @param $birthday
     * @param $etat
     * @param $praticien
     */
    private function fr_dTcaP_ado($patient,$birthday,$etat,$praticien){
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $month = $birth->diffInMonths($date_now);

        $Hib = $this->vaccinRepository->findOneBy(['vaccin_name'=>"dTcaP-ado"]);
        if ($Hib){
            if($month <= 180){
                $rdv = new RendezVous();
                $rdv->setPatient($patient);
                $rdv->setVaccin($Hib);
                $rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($month == 180){
                    if(!$date_now->isWeekend()){
                        $tomorrow = $date_now->addDay();
                        if($tomorrow->day != 6){
                            $rdv->setDateRdv($tomorrow);
                        }else{
                            $monday = $date_now->addDays(3);
                            $rdv->setDateRdv($monday);
                        }
                    }else{
                        $date = $date_now->addDays(2);
                        $rdv->setDateRdv($date);// 16mois
                    }
                }else{
                    $month_180 = $this->add_rdv_in_month(180,$birthday);
                    $rdv->setDateRdv($month_180);
                }
                $rdv->setStatus(false);
                $this->entityManager->persist($rdv);
            }
            $this->entityManager->flush();
        }
    }

    private function add_rdv_in_year($nbr_year,$birthday){
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $year = $birth->diffInYears($date_now);
        $year_diff = $nbr_year - $year;
        $days = $birth->addYears($year_diff);
        if($days->isWeekend()){
            return $days->addDays(2);
        }else{
            return $days;
        }
    }

    private function add_rdv_in_month($nbr_month,$birthday){
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $month = $birth->diffInMonths($date_now);
        $month_diff = $nbr_month - $month;
        $days = $birth->addMonths($month_diff);
        if($days->isWeekend()){
            return $days->addDays(2);
        }else{
            return $days;
        }
    }

    private function add_rdv_in_week($nbr_week,$birthday){
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $week = $birth->diffInWeeks($date_now);
        $week_diff = $nbr_week - $week;
        $days = $birth->addWeeks($week_diff);
        if($days->isWeekend()){
            return $days->addDays(2);
        }else{
            return $days;
        }
    }
}