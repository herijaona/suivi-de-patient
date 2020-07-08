<?php

namespace App\Controller\Patient;

use App\Entity\Family;
use App\Entity\GroupFamily;
use App\Entity\OrdoConsultation;
use App\Entity\OrdoVaccination;
use App\Entity\PatientOrdoConsultation;
use App\Form\RdvType;
use App\Form\VaccinType;
use App\Repository\FamilyRepository;
use App\Repository\GroupFamilyRepository;
use App\Repository\OrdoConsultationRepository;
use App\Repository\OrdonnaceRepository;
use App\Repository\OrdoVaccinationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\VaccinRepository;
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
    protected $ordonnaceRepository;
    protected $ordoConsultationRepository;
    protected $ordoVaccinationRepository;
    protected $vaccinRepository;

    function __construct(
        VaccinGenerate $vaccinGenerate,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        OrdoConsultationRepository $ordoConsultationRepository,
        OrdoVaccinationRepository $ordoVaccinationRepository,
        VaccinRepository $vaccinRepository,
        FamilyRepository $familyRepository,
        OrdonnaceRepository $ordonnaceRepository,
        GroupFamilyRepository $groupFamilyRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->vaccinGenerate = $vaccinGenerate;
        $this->vaccinRepository = $vaccinRepository;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->ordonnaceRepository = $ordonnaceRepository;
        $this->ordoConsultationRepository = $ordoConsultationRepository;
        $this->ordoVaccinationRepository = $ordoVaccinationRepository;
        $this->familyRepository = $familyRepository;
        $this->groupFamilyRepository = $groupFamilyRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="patient")
     */
    public function patient()
    {
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $birtday = $patient->getDateOnBorn();
        $event = [];

        /*$all_rdv = $patient->getRendeVous();


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
        }*/
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
     * @Route("/consultation", name="consultation_patient")
     */
    public function consultation_patient()
    {
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $doctor = $this->praticienRepository->findAll();
        $rvc = $this->ordoConsultationRepository->searchStatus($patient->getId(), 1);
        return $this->render('patient/consultation.html.twig', [
            'consultation'=>$rvc,
            'Doctors'=>$doctor,
        ]);
    }

    /**
     * @Route("/vaccination", name="vaccination_patient")
     */
    public function vaccination_patient()
    {
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $doctor = $this->praticienRepository->findAll();
        $rvc = $this->ordoVaccinationRepository->searchStatus($patient->getId(), 1);

        return $this->render('patient/vaccination.html.twig', [
            'vaccination'=>$rvc,
            'Doctors'=>$doctor,
        ]);
    }

    /**
     * @Route("/rdv/rejected", name="rdv_annuler")
     *
     */
    public function rdv_annuler()
    {
        $user = $this->getUser();
        $patient= $this->patientRepository->findOneBy(['user'=>$user]);
        $rce = $this->ordoConsultationRepository->searchStatus($patient->getId(), 2);
        $rve = $this->ordoVaccinationRepository->searchStatus($patient->getId(), 2);
        return $this->render('patient/rdv_annuler_patient.html.twig',[
            'consultation'=> $rce,
            'vaccination'=>$rve
        ]);
    }


    /**
     * @Route("/rdv/in", name="rdv_patient")
     */
    public function rdv_patient()
    {
        $user = $this->getUser();
        $patient= $this->patientRepository->findOneBy(['user'=>$user]);
        $rce = $this->ordoConsultationRepository->searchStatus($patient->getId());
        $rve = $this->ordoVaccinationRepository->searchStatus($patient->getId());
        $doctor = $this->praticienRepository->findAll();
        $vaccin= $this->vaccinRepository->findAll();
        return $this->render('patient/rdv_patient.html.twig', [
            'consultation'=>$rce,
            'vaccination'=>$rve,
            'Doctors'=>$doctor,
            'Vaccin'=>$vaccin
        ]);
    }

    /**
     * @Route("/group_family", name="group_patient")
     */
    public function group_patient()
    {
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $my_group = [];

        $mygroups = $this->familyRepository->findBy(['patientChild' => $patient]);

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
                $my_group[$i]["Membres"] = $this->familyRepository->findBy(['groupFamily' => $my_group[$i]["ID"]]);
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
        $rdvRequest = $request->request->get("rdv");
        $type = $rdvRequest["typeRdv"];
        $doctor = $rdvRequest["praticiens"];
        $vaccin = $rdvRequest["vaccin"];
        $date = $rdvRequest["dateRdv"];
        $description = $rdvRequest["description"];
        $heure = $rdvRequest["heureRdv"];
        $Id = $rdvRequest["id"];

        $user = $this->getUser();

        $rdv_date = str_replace("/", "-", $date);

        $Date_Rdv = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$heure)));

        $ordo = null;
        $vaccination = null;
        $praticien = null;

        if($doctor != ''){
            $praticien =  $this->praticienRepository->find($doctor);
            $ordo = $this->ordonnaceRepository->findOneBy(['praticien' => $praticien]);
        }

        if($vaccin != ''){
            $vaccination= $this->vaccinRepository->find($vaccin);
        }

        $patient =  $this->patientRepository->findOneBy(['user' => $user]);

        if ($type=="consultation"){
            if ($Id != ''){
                $ordoconsultation = $this->ordoConsultationRepository->find($Id);
            }else{
                $ordoconsultation = new OrdoConsultation();
            }
            $ordoconsultation->setDateRdv($Date_Rdv);
            $ordoconsultation->setObjetConsultation($description);
            $ordoconsultation->setStatusConsultation(0);
            $ordoconsultation->setPatient($patient);
            $ordoconsultation->setOrdonnance($ordo);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ordoconsultation);
            $entityManager->flush();

        }else{
            if ($Id != ''){
                $ordovaccination = $this->ordoVaccinationRepository->find($Id);
            }else{
                $ordovaccination = new OrdoVaccination();
            }
            $ordovaccination->setDatePrise($Date_Rdv);
            $ordovaccination->setOrdonnance($ordo);
            $ordovaccination->setReferencePraticienExecutant($praticien);
            $ordovaccination->setVaccin($vaccination);
            $ordovaccination->setPatient($patient);
            $ordovaccination->setStatusVaccin(0);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ordovaccination);
            $entityManager->flush();
        }
        return $this->redirectToRoute('rdv_patient');

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
            $group_family = $this->familyRepository->findOneBy(['patientChild' => $request->request->get('id_membre'), 'groupFamily' => $request->request->get('id_group')]);

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

    /**
     * @Route("/form-add-rdv", name="add_form_rdv_patient", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @return JsonResponse
     */
    public function add_form_rdv(Request $request)
    {
        $action = $request->request->get('action');
        $rdv = [];
        $typeRdvArrays = [
            "consultation" => "CONSULTATION",
            "vaccination" => "VACCINATION"
        ];
        if ($action == "new") {
            $form = $this->createForm(RdvType::class, $rdv, ['typeRdvArrays' => $typeRdvArrays]);
            $response = $this->renderView('patient/_form_rdv.html.twig', [
                'new' => true,
                'form' => $form->createView(),
                'eventData' => $rdv,
            ]);
        } else {
            $action = $request->request->get('id');
            $rdv['id'] = $request->request->get('id');
            $rdv['typeRdv'] = $request->request->get('type');
            if ($rdv['typeRdv'] == 'consultation'){
                $ordoCon = $this->ordoConsultationRepository->find($rdv['id']);
                $rdv['description'] = $ordoCon->getObjetConsultation();
                $rdv['dateRdv'] = $ordoCon->getDateRdv();
            }
            elseif ($rdv['typeRdv'] == 'vaccination'){
                $ordoCon = $this->ordoVaccinationRepository->find($rdv['id']);
                $rdv['vaccin'] = $ordoCon->getVaccin();
                $rdv['dateRdv'] = $ordoCon->getDatePrise();
            }
            if ($ordoCon->getOrdonnance() != null && $ordoCon->getOrdonnance()->getPraticien() != null) $rdv['praticiens'] = $ordoCon->getOrdonnance()->getPraticien()->getId();
            if ($rdv['dateRdv'] != ''){
                $date = $rdv['dateRdv']->format('d-m-Y H:i:s');

                $rdv['dateRdv'] = str_replace("-", "/", explode(' ', $date)[0]);
                $rdv['heureRdv'] = explode(' ', $date)[1];
            }

            $form = $this->createForm(RdvType::class, $rdv, ['typeRdvArrays' => $typeRdvArrays]);
            $response = $this->renderView('patient/_form_rdv.html.twig', [
                'new' => false,
                'form' => $form->createView(),
                'eventData' => $rdv,
            ]);
        }
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);
    }


    /**
     * @Route("/rdv/remove", name="remove_rdv_patient", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_vaccin(Request $request)
    {
        $Id = $request->request->get('id');
        $type = $request->request->get('type');
        $delete = false;
        if ($type == 'consultation'){
            $ordoCon = $this->ordoConsultationRepository->find($Id);
            if ( $ordoCon != null){
                $IntervationConsultations = $ordoCon->getIntervationConsultations();
                $PatientOrdoConsultations = $ordoCon->getPatientOrdoConsultations();
                if (($IntervationConsultations && count($IntervationConsultations) > 0) ||
                    ($PatientOrdoConsultations && count($PatientOrdoConsultations) > 0))
                {
                    $delete = false;
                    $this->addFlash('error', 'Erreur de suprimé de cet élément !');
                }else{
                    $this->entityManager->remove($ordoCon);
                    $this->entityManager->flush();
                    $delete = true;
                    $this->addFlash('success', 'Rdv à été supprimé avec succès !');
                }
            }
        }
        elseif ($type == 'vaccination'){
            $ordoCon = $this->ordoVaccinationRepository->find($Id);
            if ( $ordoCon != null){
                $InterventionVaccinations = $ordoCon->getInterventionVaccinations();
                $PatientOrdoVaccinations = $ordoCon->getPatientOrdoVaccinations();
                if (($PatientOrdoVaccinations && count($PatientOrdoVaccinations) > 0) ||
                    ($InterventionVaccinations && count($InterventionVaccinations) > 0))
                {
                    $delete = false;
                    $this->addFlash('error', 'Erreur de suprimé de cet élément !');
                }else{
                    $this->entityManager->remove($ordoCon);
                    $this->entityManager->flush();
                    $delete = true;
                    $this->addFlash('success', 'Rdv à été supprimé avec succès !');
                }
            }
        }
        return new JsonResponse(['form_delete' => $delete]);
    }

}
