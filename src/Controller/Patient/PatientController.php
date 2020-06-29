<?php

namespace App\Controller\Patient;

use App\Entity\Family;
use App\Entity\GroupFamily;
use App\Repository\FamilyRepository;
use App\Repository\GroupFamilyRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Service\VaccinGenerate;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/patient")
 */
class PatientController extends AbstractController
{
    protected $vaccinGenerate;
    protected $patientRepository;
    protected $praticienRepository;
    protected $familyRepository;
    protected $groupFamilyRepository;
    protected $entityManager;
    function __construct(
        VaccinGenerate $vaccinGenerate,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        FamilyRepository $familyRepository,
        GroupFamilyRepository $groupFamilyRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->vaccinGenerate = $vaccinGenerate;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->familyRepository = $familyRepository;
        $this->groupFamilyRepository = $groupFamilyRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="patient")
     */
    public function patient()
    {
        if ($this->isGranted('ROLE_PRATICIEN')) {
            return $this->redirectToRoute('praticien');
        }
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $birtday = $patient->getDateOnBorn();

        $all_rdv = $patient->getRendeVous();

        $event = [];
        foreach ($all_rdv as $rdv){
            $color = '#fcb41d';
            if($rdv->getEtat() == 0){
                $color = '#fcb41d';
            }else if ($rdv->getStatus() == 1){
                $color = '#51c81c';
            }else if($rdv->getType() == 1){
                $color = '#3794fc';
            }else if($rdv->getType() == 2){
                $color = '#ec37fc';
            }else if($rdv->getType() == 3){
                $color = '#fc381d';
            }
            $element = [
                    'title'=>($rdv->getVaccin() != null && $rdv->getVaccin()->getVaccinName() != null) ? $rdv->getVaccin()->getVaccinName() : ($rdv->getType() == 2 ? "Demander de consultation" : ($rdv->getType() == 3 ? 'Demander de Rendez-vous': '')),
                    'start'=>$rdv->getDateRdv()->format('Y-m-d'),
                    'id'=>$rdv->getId(),
                    'color'=> $color
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
            $dateNow = date('Y-m-d');
            //$praticien = $this->praticienRepository->find(1);
            $this->vaccinGenerate->generateCalendar($patient,$birtday,$type,$state, null, $dateNow);
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
            if($rdv->getType() == 2){
                $rdv->detail = $rdv->getDescription();
                //$rdv->dateRdv = Carbon::parse($rdv->getDateRdv())->locale('fr_FR')->isoFormat('dddd d MMMM Y');
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
            'Consultations' => $data,
            'Doctors' => $doctor,
            'type' => 2
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
            if($rdv->getType() == 1){
                $rdv->detail = $rdv->getVaccin()->getVaccinName();
                //$rdv->dateRdv = Carbon::parse($rdv->getDateRdv())->locale('fr_FR')->isoFormat('dddd d MMMM Y');
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
            'Vaccinations'=>$data,
            'type' => 1
        ]);
    }



    /**
     * @Route("/rdv", name="rdv_patient")
     */
    public function rdv_patient()
    {
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $all_rdv = $patient->getRendeVous();
        $data =[];
        $doctor = $this->praticienRepository->findAll();

        foreach ($all_rdv as $rdv){
            if($rdv->getType() == 3){
                $rdv->detail = $rdv->getDescription();
                //$rdv->dateRdv = Carbon::parse($rdv->getDateRdv())->format('d/m/y')->locale('fr_FR')->isoFormat('dddd d MMMM Y');
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

        return $this->render('patient/rdv_patient.html.twig', [
            'Consultations' => $data,
            'Doctors' => $doctor,
            'type' => 3
        ]);
    }

    /**
     * @Route("/group_family", name="group_patient")
     */
    public function group_patient()
    {
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        //$family = $this->familyRepository->findByPatientParent($patient->getId());
        $family_group = [];
        $my_group = [];

        $mygroups = $this->familyRepository->findBy(['patient_child' => $patient]);
        $m = 0;
        if($mygroups && count($mygroups) > 0){
            foreach ($mygroups as $mygroup){
                $groupFamily  = $mygroup->getGroupFamily();
                $my_group[$m]["ID"] = $groupFamily->getId();
                $my_group[$m]["Name"] = $groupFamily->getDesignation();
                $m++;
            }
        }

        if ($my_group && count($my_group) > 0){
            for ($i = 0; $i < $m; $i++) {
                $my_group[$i]["Membres"] = $this->familyRepository->findBy(['group_family' => $my_group[$i]["ID"]]);
            }
        }

        return $this->render('patient/group_patient.html.twig', [
            //'familly' => $family,
            'my_groups' => $my_group,
            'mygroup' => $mygroups,
        ]);
    }

    /**
     * @Route("/register-rdv", name="register_rdv")
     */
    public function register_rdv(Request $request)
    {
        $type_id = $request->request->get("type_id");
        $doctor = $request->request->get("doctor");
        $date = $request->request->get("date");
        $description = $request->request->get("description");
        $user = $this->getUser();
        $rdv_date = str_replace("/", "-", $date);

        $Date_Rdv = new \DateTime(date ("Y-m-d", strtotime ($rdv_date)));

        $praticien = null;
        if($doctor != ''){
            $praticien =  $this->praticienRepository->find($doctor);
        }

        $patient =  $this->patientRepository->findOneBy(['user'=>$user]);
        /*$rdv = new RendezVous();
        $rdv->setPraticien($praticien);
        $rdv->setDescription($description);
        $rdv->setType($type_id);
        $rdv->setDateRdv($Date_Rdv);
        $rdv->setPatient($patient);
        $rdv->setVaccin(null);
        $this->entityManager->persist($rdv);
        $this->entityManager->flush();*/
        if ($type_id == 1 ){
        return $this->redirectToRoute('vaccination_patient');
        }elseif ($type_id == 2 ){
            return $this->redirectToRoute('consultaion_patient');
        }elseif ($type_id == 3){
            return $this->redirectToRoute('rdv_patient');
        }

    }
    /**
     * @Route("/list_notification", name="notification_patient")
     */
    public function notification_patient(Request $request)
    {
        $user = $this->getUser();

        //$rdv_praticien = $this->rendezVousRepository->findNotification(2);
        $rdv_praticien = [];
        return $this->render('patient/notification_patient.html.twig', [
            'rdv_praticien' => $rdv_praticien
        ]);
    }

    /**
     * @Route("/register-group", name="register_group")
     */
    public function register_group(Request $request)
    {
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        if ($request->request->get('group_id') != ""){
            $group_family = $this->groupFamilyRepository->find($request->request->get('group_id'));
            $group_family->setDesignation($request->request->get('group_name'));
            $this->entityManager->persist($group_family);
            $this->entityManager->flush();
        }else{
            $group_family = new GroupFamily();
            $group_family->setDesignation($request->request->get('group_name'));
            $group_family->setPatient($patient);
            $this->entityManager->persist($group_family);
            $family = new Family();
            $family->setGroupFamily($group_family);
            $family->setPatientChild($patient);
            $this->entityManager->persist($family);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('group_patient');
    }

    /**
     * @Route("/add-new-membres-group", name="add_new_membres_group")
     */
    public function add_new_membres_group(Request $request)
    {
        $user = $this->getUser();
        if ($request->request->get('group_id') != "" && $request->request->get('patient')){
            $group_family = $this->groupFamilyRepository->find($request->request->get('group_id'));
            $patient = $this->patientRepository->find($request->request->get('patient'));
            $family = new Family();
            $family->setGroupFamily($group_family);
            $family->setPatientChild($patient);
            $this->entityManager->persist($family);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('see_my_group', array('group_id' => $request->request->get('group_id')));
    }

    /**
     * @Route("/delete-membres-group", name="delete_membres_group")
     */
    public function delete_membres_group(Request $request)
    {
        $user = $this->getUser();
        if ($request->request->get('id_group') != "" && $request->request->get('id_membre')){
            $group_family = $this->familyRepository->findOneBy(['patient_child' => $request->request->get('id_membre'), 'group_family' => $request->request->get('id_group')]);

            if($group_family){
                $this->entityManager->remove($group_family);
                $this->entityManager->flush();
            }
        }
        return new JsonResponse(['status' => true]);
    }

    /**
     * @Route("/see-group-membres/{group_id}", name="see_my_group")
     */
    public function see_my_group(Request $request, $group_id)
    {
        $user = $this->getUser();
        $patient_child = [];
        $currentPatient = $this->patientRepository->findOneBy(['user'=>$user]);
        if($currentPatient){
            array_push($patient_child, $currentPatient->getId());
        }
        $patients = $this->patientRepository->findAll();
        $group = $this->groupFamilyRepository->find($group_id);
        if($group){
            if(!in_array($group->getPatient()->getId(), $patient_child)){
                array_push($patient_child, $group->getPatient()->getId());
            }
        }

        $membres = $this->familyRepository->getPatientByIdFamily($group_id, $patient_child);
        $membres_group = [];
        $membres_group_id = [];
        if ($membres && count($membres) > 0){
            foreach ($membres as $membre){
                array_push($membres_group, $membre->getPatientChild());
            }
        }
        $membres_patients = [];

        if ($membres_group && count($membres_group) > 0){
            foreach ($membres_group as $membre_group){
                array_push($membres_group_id, $membre_group->getId());
            }
        }

        if ($patients && count($patients) > 0){
            foreach ($patients as $patient){
                if($patient->getId() != $currentPatient->getId() && !in_array($patient->getId(), $membres_group_id)){
                    array_push($membres_patients, $patient);
                }
            }
        }

        return $this->render('patient/group_membres_patient.html.twig', [
            'membres' => $membres_group,
            'group' => $group,
            'patients' => $membres_patients
        ]);
    }

}
