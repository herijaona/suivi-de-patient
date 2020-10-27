<?php


namespace App\Service;


use App\Entity\CarnetVaccination;
use App\Entity\OrdoVaccination;
use App\Entity\PatientCarnetVaccination;
use App\Repository\InterventionVaccinationRepository;
use App\Repository\VaccinRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Exception;

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
        $enceinte= $patient->getIsEnceinte();
        $state = $patient->getState()->getNameState();
        $birthday = $patient->getDateOnBorn();
        $dateEnceinte= $patient->getDateEnceinte();
        $listVaccin = [];
        $day_preg =  $dateNow;
        if($state != null) {
            switch ($type_patient) {
                case 'ENFANT':
                    $alls = $this->vaccinRepository->findVaccinByTYpe('ENFANT', $state);
                    $listVaccin = $this->generate_vaccin($patient, $birthday, $alls);
                    break;
                case 'ADULTE':
                    $alls = $this->vaccinRepository->findVaccinByTYpe('ADULTE',$state);
                    $listVaccin = $this->generate_vaccin($patient, $birthday, $alls);
                    break;
            }
            if ($type_patient == "ADULTE" && $enceinte == 1) {
                $alls = $this->vaccinRepository->findVaccinByTYpe('FEMME ENCEINTE',$state);
                $listVaccin = $this->generate_vaccin($patient, $dateEnceinte, $alls);
            }
        }
        return $listVaccin;
    }

    /**
     * @param $patient
     * @param $birthday
     * @param $vaccinAll
     * @param $IntervationVaccination
     * @throws Exception
     */
    public function generate_vaccin($patient, $birthday, $vaccinAll)
    {
        foreach ( $vaccinAll as $vacc){
            if($vacc != null){

                $idvaccin = $vacc->getIdVaccin();
                $identification = $vacc->getStatut();
                // Get Vaccin Methods that return datePriseInitiale and rappels
                $vaccMethods = get_class_methods($vacc);
                $getDateMethods = array();
                foreach ($vaccMethods as $meth) {
                    if(strpos($meth, "getRappel") === 0 || $meth === "getDatePrise"){
                        array_push($getDateMethods, $meth);
                    }
                }

                // Calculate the exact date for each string formatted date
                foreach($getDateMethods as $getDate){
                    $crnV = new CarnetVaccination();


                    $crnV->setPatient($patient)
                        ->setVaccin($vacc)
                        ->setEtat(false)
                        ->setIdentification($identification)
                        ->setIdentifiantVaccin($idvaccin)
                    ;

                    $getVAcc = $vacc->$getDate();


                    if($getVAcc !== "" && $getVAcc !== null){

                        $interval = date_interval_create_from_date_string($getVAcc);
                        $rappelOrDateInit = new \Datetime($birthday->format('Y-m-d H:i:s'));
                        date_add($rappelOrDateInit, $interval);

                        // Reporter la date si elle tombe en week-end
                        $weekday = date('N', $rappelOrDateInit->getTimestamp());

                        if($weekday === "7"){
                            date_add($rappelOrDateInit, date_interval_create_from_date_string("1 day"));
                        }
                        elseif($weekday === "6"){
                            date_add($rappelOrDateInit, date_interval_create_from_date_string("2 days"));
                        }
                        elseif($weekday === "5"){
                            date_add($rappelOrDateInit, date_interval_create_from_date_string("3 days"));
                        }

                        if($getDate === "getDatePrise"){
                            $crnV->setDatePrise($rappelOrDateInit);
                        }

                        $this->entityManager->persist($crnV);
                        $this->entityManager->flush();
                        $PatientCarnet = new PatientCarnetVaccination();
                        $PatientCarnet->setPatient($patient)
                            ->setCarnetVaccination($crnV);
                        $this->entityManager->persist($PatientCarnet);
                        $this->entityManager->flush();
                    }
                }
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