<?php

namespace App\Controller\Patient;

use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Service\VaccinGenerate;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/patient")
 */
class PatientController extends AbstractController
{
    protected $vaccinGenerate;
    protected $patientRepository;
    protected $praticienRepository;
    function __construct(VaccinGenerate $vaccinGenerate,PatientRepository $patientRepository,PraticienRepository $praticienRepository)
    {
        $this->vaccinGenerate = $vaccinGenerate;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
    }

    /**
     * @Route("/", name="patient")
     */
    public function patient()
    {
        if (!$this->isGranted('ROLE_PATIENT')) {
            return $this->redirectToRoute('homepage');
        }
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $all_rdv = $patient->getRendeVous();
        $event = [];
        foreach ($all_rdv as $rdv){
            $element = [
                    'title'=>$rdv->getVaccin()->getVaccinName(),
                    'start'=>$rdv->getDateRdv()->format('Y-m-d'),
                    'id'=>$rdv->getId(),
                    'color'=> $rdv->getEtat() == 0 ? '#1e7e34':'#1fdc25'
                ];
            array_push($event,$element);
        }
        return $this->render('patient/patient.html.twig', [
            'controller_name' => 'PatientController',
            'Events'=>$event
        ]);
    }

    /**
     * @Route("/generate/vaccin", name="generate_vaccin_patient")
     */
    public function generateVaccin(){
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        if($patient){
            $type = $patient->getTypePatient()->getTypePatientName();
            $state = $patient->getAdressOnBorn()->getRegion()->getState()->getNameState();
            $birtday = $patient->getDateOnBorn();
            $this->vaccinGenerate->generateCalendar($patient,$birtday,$type,$state);
            return new JsonResponse("ok");
        }
        return "error";
       // $this->vaccinGenerate->
    }

    /**
     * @Route("/consultation", name="consultaion_patient")
     */
    public function consultaion_patient()
    {
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $all_rdv = $patient->getRendeVous();
        $data =[];
        $doctor = $this->praticienRepository->findAll();
        foreach ($all_rdv as $rdv){
            if($rdv->getType()==2){
                $rdv->detail = $rdv->getDescription();
                $rdv->dateRdv = Carbon::parse($rdv->getDateRdv())->locale('fr_FR')->isoFormat('dddd d MMMM Y');
                if($rdv->getPraticien()){
                    $rdv->nom = $rdv->getPraticien()->getFirstName()." ".$rdv->getPraticien()->getLastName();
                    $rdv->phone = $rdv->getPraticien()->getPhone();
                }else{
                    $rdv->nom  = "";
                    $rdv->phone = "";
                }
                array_push($data,$rdv);
            }
        }

        return $this->render('patient/consultation.html.twig', [
            'controller_name' => 'PatientController',
            'Consultations'=>$data,
            'Doctors'=>$doctor
        ]);
    }

    /**
     * @Route("/vaccination", name="vaccination_patient")
     */
    public function vaccination_patient()
    {
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $all_rdv = $patient->getRendeVous();
        $data =[];
        foreach ($all_rdv as $rdv){
            if($rdv->getType()==1){
                $rdv->detail = $rdv->getVaccin()->getVaccinName();
                $rdv->dateRdv = Carbon::parse($rdv->getDateRdv())->locale('fr_FR')->isoFormat('dddd d MMMM Y');
                if($rdv->getPraticien()){
                    $rdv->nom = $rdv->getPraticien()->getFirstName()." ".$rdv->getPraticien()->getLastName();
                    $rdv->phone = $rdv->getPraticien()->getPhone();
                }else{
                    $rdv->nom  = "";
                    $rdv->phone = "";
                }
                array_push($data,$rdv);
            }
        }
        return $this->render('patient/vaccination.html.twig', [
            'controller_name' => 'PatientController',
            'Vaccinations'=>$data
        ]);
    }
}
