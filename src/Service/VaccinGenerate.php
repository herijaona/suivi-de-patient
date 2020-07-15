<?php


namespace App\Service;


use App\Entity\CarnetVaccination;
use App\Entity\OrdoVaccination;
use App\Entity\RendezVous;
use App\Repository\VaccinRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

class VaccinGenerate
{
    protected $entityManager;
    protected $vaccinRepository;
    function __construct(EntityManagerInterface $entityManager,VaccinRepository $vaccinRepository)
    {
        $this->entityManager = $entityManager;
        $this->vaccinRepository = $vaccinRepository;
    }

    public function generateCalendar($patient,$birthday,$type_patient,$state,$praticien=null, $dateNow)
    {
        $etat = 0;
        if($praticien) $etat = 1;
        $day_preg =  $dateNow;
        if(strtoupper($state)=='CAMEROUN'){
            switch ($type_patient){
                case 'ENFANT':
                    $this->ca_vaccin_enfant($patient,$birthday,'Antituberculeux : B.C.G',1, $etat, $praticien);
                    $this->ca_vaccin_enfant($patient,$birthday,'DTC – HepB + Hib 1',6, $etat, $praticien);
                    $this->ca_vaccin_enfant($patient,$birthday,'DTC-HepB2 + Hib2',10, $etat, $praticien);
                    $this->ca_vaccin_enfant($patient,$birthday,'DTC-HepB2 + Hib3',14, $etat, $praticien);
                    $this->ca_vaccin_enfant($patient,$birthday,'Pneumo 13-1 (VPO-1 + Rota1)',6, $etat, $praticien);
                    $this->ca_vaccin_enfant($patient,$birthday,'Pneumo 13-2 (VPO-2 + Rota2)',10, $etat, $praticien);
                    $this->ca_vaccin_enfant($patient,$birthday,'Pneumo 13-3 (VPO-3)',14, $etat, $praticien);
                    $this->ca_vaccin_enfant($patient,$birthday,'VAR + VAA',36, $etat, $praticien);
                    break;
                case 'ADULTE':
                    $this->ca_vaccin_adulte($patient,$birthday,$etat,$patient);
                    $this->ca_vaccin_adult_age3($patient,$birthday,'Coqueluche acellulaire (ca)',25,$etat,$praticien);
                    $this->ca_vaccin_adult_age3($patient,$birthday,'Zona',65,$etat,$praticien);
                    $this->ca_grippe($patient,$birthday,$etat,$praticien);
                    break;
                case 'FEMME ENCEINTE':
                    $this->ca_enceinte($patient,$day_preg,'VAT1',0,$etat,$praticien,1);
                    $this->ca_enceinte($patient,$day_preg,'VAT2',1,$etat,$praticien);
                    $this->ca_enceinte($patient,$day_preg,'VAT3',7,$etat,$praticien);
                    $this->ca_enceinte($patient,$day_preg,'VAT4',19,$etat,$praticien);
                    $this->ca_enceinte($patient,$day_preg,'VAT5',31,$etat,$praticien);
                    break;
            }
        }else{
            switch ($type_patient){
                case 'ENFANT':
                    $this->fr_DTCaP($patient,$birthday,$etat,$praticien);
                    $this->fr_Hib_hepb_pnc($patient,$birthday,'Hib',$etat,$praticien);
                    $this->fr_Hib_hepb_pnc($patient,$birthday,'Hep B',$etat,$praticien);
                    $this->fr_Hib_hepb_pnc($patient,$birthday,'Pnc',$etat,$praticien);
                    $this->fr_dTcaP_ado($patient,$birthday,$etat,$praticien);
                    $this->fr_ROR($patient,$birthday,$etat,$praticien);
                    $this->fr_MnC($patient,$birthday,$etat,$praticien);
                    $this->ca_vaccin_enfant($patient,$birthday,'Antituberculeux : B.C.G',1,$etat,$praticien);
                    break;
            }
        }
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
                $rdv->setEtat($etat);
                $rdv->setReferencePraticienExecutant($praticien);
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
                $rdv->setStatusVaccin(false);
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

        $vaccin = $this->vaccinRepository->findOneBy(['vaccinName'=>'Calendrier Vaccin Adulte']);
        if ($vaccin){
            if($year <= 25){
                for ($i=1; $i<=7; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 25){
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
                                $rdv->setDatePrise($date);// 4mois
                            }
                        }else{
                            $year_25 = $this->add_rdv_in_year(25,$birthday);
                            $rdv->setDatePrise($year_25);
                        }
                    }elseif ($i==2){
                        $year_45 = $this->add_rdv_in_year(45,$birthday);
                        $rdv->setDatePrise($year_45);
                    }elseif ($i==3){
                        $year_65 = $this->add_rdv_in_year(65,$birthday);
                        $rdv->setDatePrise($year_65);
                    }elseif ($i==4){
                        $year_75 = $this->add_rdv_in_year(75,$birthday);
                        $rdv->setDatePrise($year_75);
                    }elseif ($i==5){
                        $year_85 = $this->add_rdv_in_year(85,$birthday);
                        $rdv->setDatePrise($year_85);
                    }elseif ($i==6){
                        $year_95 = $this->add_rdv_in_year(95,$birthday);
                        $rdv->setDatePrise($year_95);
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDatePrise($year_105);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }

            }elseif($year<=45){
                for ($i=1; $i<=6; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    //$rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 45){
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
                                $rdv->setDatePrise($date);// 4mois
                            }
                        }else{
                            $year_45 = $this->add_rdv_in_year(45,$birthday);
                            $rdv->setDatePrise($year_45);
                        }
                    }elseif ($i==2){
                        $year_65 = $this->add_rdv_in_year(65,$birthday);
                        $rdv->setDatePrise($year_65);
                    }elseif ($i==3){
                        $year_75 = $this->add_rdv_in_year(75,$birthday);
                        $rdv->setDatePrise($year_75);
                    }elseif ($i==4){
                        $year_85 = $this->add_rdv_in_year(85,$birthday);
                        $rdv->setDatePrise($year_85);
                    }elseif ($i==5){
                        $year_95 = $this->add_rdv_in_year(95,$birthday);
                        $rdv->setDatePrise($year_95);
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDatePrise($year_105);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($year<=65){
                for ($i=1; $i<=5; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    //$rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 65){
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
                                $rdv->setDatePrise($date);// 4mois
                            }
                        }else{
                            $year_65 = $this->add_rdv_in_year(65,$birthday);
                            $rdv->setDatePrise($year_65);
                        }
                    }elseif ($i==2){
                        $year_75 = $this->add_rdv_in_year(75,$birthday);
                        $rdv->setDatePrise($year_75);
                    }elseif ($i==3){
                        $year_85 = $this->add_rdv_in_year(85,$birthday);
                        $rdv->setDatePrise($year_85);
                    }elseif ($i==4){
                        $year_95 = $this->add_rdv_in_year(95,$birthday);
                        $rdv->setDatePrise($year_95);
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDatePrise($year_105);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($year<=75){
                for ($i=1; $i<=4; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    //$rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 75){
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
                                $rdv->setDatePrise($date);// 4mois
                            }
                        }else{
                            $year_75 = $this->add_rdv_in_year(75,$birthday);
                            $rdv->setDatePrise($year_75);
                        }
                    }elseif ($i==2){
                        $year_85 = $this->add_rdv_in_year(85,$birthday);
                        $rdv->setDatePrise($year_85);
                    }elseif ($i==3){
                        $year_95 = $this->add_rdv_in_year(95,$birthday);
                        $rdv->setDatePrise($year_95);
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDatePrise($year_105);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($year<=85){
                for ($i=1; $i<=3; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                   // $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 85){
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
                                $rdv->setDatePrise($date);// 4mois
                            }
                        }else{
                            $year_85 = $this->add_rdv_in_year(85,$birthday);
                            $rdv->setDatePrise($year_85);
                        }
                    }elseif ($i==2){
                        $year_95 = $this->add_rdv_in_year(95,$birthday);
                        $rdv->setDatePrise($year_95);
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDatePrise($year_105);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($year<=95){
                for ($i=1; $i<=2; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    //$rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($year == 95){
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
                                $rdv->setDatePrise($date);// 4mois
                            }
                        }else{
                            $year_95 = $this->add_rdv_in_year(95,$birthday);
                            $rdv->setDatePrise($year_95);
                        }
                    }else{
                        $year_105 = $this->add_rdv_in_year(105,$birthday);
                        $rdv->setDatePrise($year_105);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($year<=105){
                $rdv = new OrdoVaccination();
                $rdv->setPatient($patient);
                $rdv->setVaccin($vaccin);
                //$rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($year == 105){
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
                        $rdv->setDatePrise($date);// 4mois
                    }
                }else{
                    $year_105 = $this->add_rdv_in_year(105,$birthday);
                    $rdv->setDatePrise($year_105);
                }
                $rdv->setEtat(false);
                $this->entityManager->persist($rdv);

            }
        }
    }

    /**
     * generate vaccin Coqueluche acellulaire (ca),Zona for parent cameroun
     * @param $patient
     * @param $birthday
     * @param  $vaccinName
     * @param $_year
     * @param $etat
     * @param $praticien
     */
    public function ca_vaccin_adult_age3($patient,$birthday,$vaccinName,$_year,$etat,$praticien)
    {
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $year = $birth->diffInYears($date_now);

        $vaccin = $this->vaccinRepository->findOneBy(['vaccinName'=>$vaccinName]);
        if ($vaccin){
            if($year <= $_year){
                $rdv = new OrdoVaccination();
                $rdv->setPatient($patient);
                $rdv->setVaccin($vaccin);
                //$rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($year == $_year){
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
                        $rdv->setDatePrise($date);// 4mois
                    }
                }else{
                    $year_ = $this->add_rdv_in_year($_year,$birthday);
                    $rdv->setDatePrise($year_);
                }
                $rdv->setEtat(false);
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

        $vaccin = $this->vaccinRepository->findOneBy(['vaccinName'=>'Grippe']);
        if ($vaccin){
            if($year >= 65){
                for ($year;$year<=105;$year++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($vaccin);
                    //$rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    $year_ = $this->add_rdv_in_year($year,$birthday);
                    $rdv->setDatePrise($year_);
                    $rdv->setEtat(false);
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
     * @param $vaccinName
     * @param $_month
     * @param $etat
     * @param $praticien
     * @param null $type
     */
    public function ca_enceinte($patient,$day_preg,$vaccinName,$_month,$etat,$praticien,$type=null)
    {
        $preg = Carbon::parse($day_preg);
        $date_now = Carbon::now();
        $month = $preg->diffInMonths($date_now);

        $vaccin = $this->vaccinRepository->findOneBy(['vaccinName'=>$vaccinName]);
        if ($vaccin){
            if(!$type){
                $rdv = new OrdoVaccination();
                $rdv->setPatient($patient);
                $rdv->setVaccin($vaccin);
                //$rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                $rdv->setEtat(false);
                $month_ = $this->add_rdv_in_month($_month,$preg);
                $rdv->setDatePrise($month_);
                $this->entityManager->persist($rdv);
            }else{
                $rdv = new OrdoVaccination();
                $rdv->setPatient($patient);
                $rdv->setVaccin($vaccin);
                //$rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                $rdv->setDatePrise($preg);
                $rdv->setEtat(false);
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

        $dtcap = $this->vaccinRepository->findOneBy(['vaccinName'=>'DTCaP']);
        if ($dtcap){
            if($month <= 4){
                for ($i=1; $i<=4; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($dtcap);
                   // $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($month == 4){
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
                                $rdv->setDatePrise($date);// 4mois
                            }
                        }else{
                            $month_4 = $this->add_rdv_in_month(4,$birthday);
                            $rdv->setDatePrise($month_4);
                        }
                    }elseif ($i==2){
                        $month_5 = $this->add_rdv_in_month(5,$birthday);
                        $rdv->setDatePrise($month_5);
                    }elseif ($i==3){
                        $month_12 = $this->add_rdv_in_month(12,$birthday);
                        $rdv->setDatePrise($month_12);
                    }else{
                        $month_132 = $this->add_rdv_in_month(132,$birthday);
                        $rdv->setDatePrise($month_132);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month == 5){
                for ($i=1; $i<=3; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($dtcap);
                    $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
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
                            $rdv->setDatePrise($date);// 5mois
                        }
                    }elseif ($i==2){
                        $month_12 = $this->add_rdv_in_month(12,$birthday);
                        $rdv->setDatePrise($month_12);
                    }else{
                        $month_132 = $this->add_rdv_in_month(132,$birthday);
                        $rdv->setDatePrise($month_132);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month <= 12){
                for ($i=1; $i<=2; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($dtcap);
                    //$rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($month == 12){
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
                                $rdv->setDatePrise($date);// 12mois
                            }
                        }else{
                            $month_12 = $this->add_rdv_in_month(12,$birthday);
                            $rdv->setDatePrise($month_12);
                        }
                    }else{
                        $month_132 = $this->add_rdv_in_month(132,$birthday);
                        $rdv->setDatePrise($month_132);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month <= 132){
                $rdv = new OrdoVaccination();
                $rdv->setPatient($patient);
                $rdv->setVaccin($dtcap);
                //$rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($month == 132){
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
                        $rdv->setDatePrise($date);// 132mois
                    }
                }else{
                    $month_132 = $this->add_rdv_in_month(132,$birthday);
                    $rdv->setDatePrise($month_132);
                }
                $rdv->setEtat(false);
                $this->entityManager->persist($rdv);
            }
            $this->entityManager->flush();
        }

    }

    /**
     * generate vaccin Hib, Hep B Pnc for child france
     * @param $patient
     * @param $birthday
     * @param $vaccinName
     * @param $etat
     * @param $praticien
     */
    private function fr_Hib_hepb_pnc($patient,$birthday,$vaccinName,$etat,$praticien){
        $birth = Carbon::parse($birthday);
        $date_now = Carbon::now();
        $month = $birth->diffInMonths($date_now);

        $Hib = $this->vaccinRepository->findOneBy(['vaccinName'=>$vaccinName]);
        if ($Hib){
            if($month <= 4){
                for ($i=1; $i<=3; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($Hib);
                   // $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($month == 4){
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
                                $rdv->setDatePrise($date);// 4mois
                            }
                        }else{
                            $month_4 = $this->add_rdv_in_month(4,$birthday);
                            $rdv->setDatePrise($month_4);
                        }
                    }elseif ($i==2){
                        $month_5 = $this->add_rdv_in_month(5,$birthday);
                        $rdv->setDatePrise($month_5);
                    }else{
                        $month_12 = $this->add_rdv_in_month(12,$birthday);
                        $rdv->setDatePrise($month_12);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month == 5){
                for ($i=1; $i<=2; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($Hib);
                   // $rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
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
                            $rdv->setDatePrise($date);// 5mois
                        }
                    }else{
                        $month_12 = $this->add_rdv_in_month(12,$birthday);
                        $rdv->setDatePrise($month_12);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month <= 12){
                $rdv = new OrdoVaccination();
                $rdv->setPatient($patient);
                $rdv->setVaccin($Hib);
                //$rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($month == 12){
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
                        $rdv->setDatePrise($date);// 12mois
                    }
                }else{
                    $month_12 = $this->add_rdv_in_month(12,$birthday);
                    $rdv->setDatePrise($month_12);
                }
                $rdv->setEtat(false);
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

        $Hib = $this->vaccinRepository->findOneBy(['vaccinName'=>"MnC"]);
        if ($Hib){
            if($month <= 11){
                for ($i=1; $i<=2; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($Hib);
                    //$rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($month == 11){
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
                                $rdv->setDatePrise($date);// 11mois
                            }
                        }else{
                            $month_11 = $this->add_rdv_in_month(11,$birthday);
                            $rdv->setDatePrise($month_11);
                        }
                    }else{
                        $month_16 = $this->add_rdv_in_month(16,$birthday);
                        $rdv->setDatePrise($month_16);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month <= 16){
                $rdv = new OrdoVaccination();
                $rdv->setPatient($patient);
                $rdv->setVaccin($Hib);
                //$rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($month == 16){
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
                        $rdv->setDatePrise($date);// 16mois
                    }
                }else{
                    $month_16 = $this->add_rdv_in_month(16,$birthday);
                    $rdv->setDatePrise($month_16);
                }
                $rdv->setEtat(false);
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

        $Hib = $this->vaccinRepository->findOneBy(['vaccinName'=>"ROR"]);
        if ($Hib){
            if($month <= 16){
                for ($i=1; $i<=2; $i++){
                    $rdv = new OrdoVaccination();
                    $rdv->setPatient($patient);
                    $rdv->setVaccin($Hib);
                    //$rdv->setPraticien($praticien);
                    $rdv->setEtat($etat);
                    if($i==1){
                        if($month == 16){
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
                                $rdv->setDatePrise($date);// 16mois
                            }
                        }else{
                            $month_16 = $this->add_rdv_in_month(16,$birthday);
                            $rdv->setDatePrise($month_16);
                        }
                    }else{
                        $month_72 = $this->add_rdv_in_month(72,$birthday);
                        $rdv->setDatePrise($month_72);
                    }
                    $rdv->setEtat(false);
                    $this->entityManager->persist($rdv);
                }
            }elseif($month <= 72){
                $rdv = new OrdoVaccination();
                $rdv->setPatient($patient);
                $rdv->setVaccin($Hib);
                //$rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($month == 72){
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
                        $rdv->setDatePrise($date);// 16mois
                    }
                }else{
                    $month_72 = $this->add_rdv_in_month(72,$birthday);
                    $rdv->setDatePrise($month_72);
                }
                $rdv->setEtat(false);
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

        $Hib = $this->vaccinRepository->findOneBy(['vaccinName'=>"dTcaP-ado"]);
        if ($Hib){
            if($month <= 180){
                $rdv = new OrdoVaccination();
                $rdv->setPatient($patient);
                $rdv->setVaccin($Hib);
                //$rdv->setPraticien($praticien);
                $rdv->setEtat($etat);
                if($month == 180){
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
                        $rdv->setDatePrise($date);// 16mois
                    }
                }else{
                    $month_180 = $this->add_rdv_in_month(180,$birthday);
                    $rdv->setDatePrise($month_180);
                }
                $rdv->setEtat(false);
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