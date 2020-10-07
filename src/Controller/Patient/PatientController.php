<?php

namespace App\Controller\Patient;

use App\Entity\Associer;
use App\Entity\Family;
use App\Entity\GroupFamily;
use App\Entity\IntervationConsultation;
use App\Entity\InterventionVaccination;
use App\Entity\OrdoConsultation;
use App\Entity\OrdoVaccination;
use App\Entity\Praticien;
use App\Form\CarnetType;
use App\Form\RdvType;
use App\Form\GenerationType;
use App\Repository\AssocierRepository;
use App\Repository\CarnetVaccinationRepository;
use App\Repository\CentreHealthRepository;
use App\Repository\CityRepository;
use App\Repository\FamilyRepository;
use App\Repository\FonctionRepository;
use App\Repository\GroupFamilyRepository;
use App\Repository\IntervationConsultationRepository;
use App\Repository\InterventionVaccinationRepository;
use App\Repository\OrdoConsultationRepository;
use App\Repository\OrdonnaceRepository;
use App\Repository\OrdoVaccinationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\PropositionRdvRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use App\Repository\VaccinRepository;
use App\Service\VaccinGenerate;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    protected $propositionRdvRepository;
    protected $userRepository;
    protected $carnetVaccinationRepository;
    protected $associerRepository;
    protected $interventionVaccinationRepository;
    protected $cityRepository;
    protected $centreHealthRepository;
    protected $fonctionRepository;
    protected $stateRepository;
    protected $intervationConsultationRepository;

    function __construct(
        VaccinGenerate $vaccinGenerate,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        OrdoConsultationRepository $ordoConsultationRepository,
        OrdoVaccinationRepository $ordoVaccinationRepository,
        StateRepository $stateRepository,
        PropositionRdvRepository $propositionRdvRepository,
        CityRepository $cityRepository,
        CentreHealthRepository $centreHealthRepository,
        IntervationConsultationRepository $intervationConsultationRepository,
        CarnetVaccinationRepository $carnetVaccinationRepository,
        VaccinRepository $vaccinRepository,
        InterventionVaccinationRepository $interventionVaccinationRepository,
        FamilyRepository $familyRepository,
        AssocierRepository $associerRepository,
        FonctionRepository $fonctionRepository,
        OrdonnaceRepository $ordonnaceRepository,
        UserRepository $userRepository,
        GroupFamilyRepository $groupFamilyRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->stateRepository=$stateRepository;
        $this->fonctionRepository=$fonctionRepository;
        $this->intervationConsultationRepository=$intervationConsultationRepository;
        $this->userRepository= $userRepository;
        $this->vaccinGenerate = $vaccinGenerate;
        $this->cityRepository= $cityRepository;
        $this->associerRepository = $associerRepository;
        $this->centreHealthRepository= $centreHealthRepository;
        $this->vaccinRepository = $vaccinRepository;
        $this->patientRepository = $patientRepository;
        $this->interventionVaccinationRepository= $interventionVaccinationRepository;
        $this->carnetVaccinationRepository= $carnetVaccinationRepository;
        $this->praticienRepository = $praticienRepository;
        $this->ordonnaceRepository = $ordonnaceRepository;
        $this->ordoConsultationRepository = $ordoConsultationRepository;
        $this->ordoVaccinationRepository = $ordoVaccinationRepository;
        $this->propositionRdvRepository = $propositionRdvRepository;
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
        $doctor = $this->praticienRepository->findAll();
        $rvc = $this->carnetVaccinationRepository->searchCarnet($patient);

        return $this->render('patient/vaccination.html.twig', [
            'vaccination'=>$rvc,
            'Doctors'=>$doctor,
        ]);
    }

    /**
     * @Route("/intervention/form",name="add_rdv_carnet")
     */
    public function add_rdv_carnet(Request $request, TranslatorInterface $translator)
    {
        $carnet = [];
        $carnet['id'] = $request->request->get('id');
        $carnetvaccina = $this->carnetVaccinationRepository->find($carnet['id'] );
        $carnet['patient']= $carnetvaccina->getPatient()->getId();
        $carnet['vaccin']= $carnetvaccina->getVaccin()->getId();
        $carnet['Praticien']= $carnetvaccina->getPraticien();
        $carnet['date']= $carnetvaccina->getDatePrise();
        $date = $carnet['date']->format('d-m-Y H:i:s');
        $carnet['date'] = str_replace("-", "/", explode(' ', $date)[0]);
        $carnet['heure'] = explode(' ', $date)[1];


        $form = $this->createForm(CarnetType::class, $carnet);
        $response = $this->renderView('patient/_form_intervention.html.twig', [
            'new' => false,
            'form' => $form->createView(),
            'eventData' => $carnet,
        ]);
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);
    }

    /**
     * @Route("/intervention/accept" , name="accept_intervention")
     * @throws Exception
     */
    public function accept_intervention(Request $request, TranslatorInterface $translator)
    {
        $carnetRequest = $request->request->get('carnet');
        $date = $carnetRequest['date'];
        $heure = $carnetRequest['heure'];
        $rdv_date = str_replace("/", "-", $date);
        $Date_Rdv = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$heure)));


        $praticien = $carnetRequest['Praticien'];
        $vaccin = $carnetRequest['vaccin'];
        $vaccin= $this->vaccinRepository->find($vaccin);
        $praticien = $this->praticienRepository->find($praticien);
        $ordonace = $this->ordonnaceRepository->findOneBy(['praticien'=>$praticien]);
        $patient = $carnetRequest['patient'];
        $patient= $this->patientRepository->find($patient);
        $Id = $carnetRequest['id'];
        $car = $this->carnetVaccinationRepository->find($Id);
        $intervention = new InterventionVaccination();
        $intervention->setEtat("0");
        $intervention->setDatePriseVaccin($Date_Rdv);
        $intervention->setVaccin($vaccin);
        $intervention->setPatient($patient);
        $intervention->setOrdonnace($ordonace);
        $intervention->setStatusVaccin("0");
        $intervention->setCarnet($car);
        $this->entityManager->persist($intervention);
        $this->entityManager->flush();
         $message=$translator->trans('successful');
        $this->addFlash('success', $message);
        return $this->redirectToRoute('patient');


    }




    /**
     * @Route("/proposition/rdv" , name ="proposisition_rdv")
     */
    public function proposition_rdv()
    {
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $propos = $this->propositionRdvRepository->searchProposition($patient);
        $pro = $this->propositionRdvRepository->searchPropositio($patient);
        return $this->render('patient/proposition.html.twig', [
            'proposition'=>$propos,
            'propos'=>$pro
        ]);

    }
    /**
     * @Route("/proposition/accepted", name = "proposition")
     */
    public function proposition_accepted(Request $request,TranslatorInterface $translator)
    {
        $id = $request->request->get("id");
        $propos = $this->propositionRdvRepository->find($id);
        if($propos != null){
            $propos->setStatusProposition(1);
            $this->entityManager->persist($propos);
            $this->entityManager->flush();
        }

        $message = $translator->trans('Proposition Appointment Accepted');
        $this->addFlash('success', $message);
        return new JsonResponse(['status' => 'OK']);


    }




    /**
     * @Route("/create-rdv", name="create_rdv")
     * @param Request $request
     * @return Response
     */
    public function create_rdv(Request $request)
    {
        $typeRdvArrays = [
             "consultation" => "CONSULTATION",
             "intervention" =>"INTERVENTION"
            ];
        $rdv = [];
        $form = $this->createForm(RdvType::class, $rdv, ['typeRdvArrays' => $typeRdvArrays]);
        return $this->render('patient/_form_rdv.html.twig', [
            'new' => true,
            'form' => $form->createView(),
            'eventData' => $rdv,
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
        $intervention = $this->intervationConsultationRepository->searchStatusInter($patient->getId());

        return $this->render('patient/rdv_patient.html.twig', [
            'consultation'=>$rce,
            'intervention'=>$intervention,
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
     * @throws Exception
     */
    public function register_rdv(Request $request,TranslatorInterface $translator)
    {

        $rdvRequest = $request->request->get("rdv");
        $doctor= $request->request->get("praticien");
        $type = $rdvRequest["typeRdv"];
        $description = $rdvRequest["objet"];
        $Id = $rdvRequest["id"];
        $user = $this->getUser();
        $ordo = null;
        $praticien = null;
        if($doctor != ''){
            $praticien =  $this->praticienRepository->find($doctor);
            $ordo = $this->ordonnaceRepository->findOneBy(['praticien' => $praticien]);

        }
        $patient =  $this->patientRepository->findOneBy(['user' => $user]);
        switch ($type){
            case 'consultation':
                if ($Id != ''){
                    $ordoconsultation = $this->ordoConsultationRepository->find($Id);
                }else{
                    $ordoconsultation = new OrdoConsultation();
                }

                $ordoconsultation->setObjetConsultation($description);
                $ordoconsultation->setStatusConsultation(0);
                $ordoconsultation->setEtat(0);
                $ordoconsultation->setPatient($patient);
                $ordoconsultation->setOrdonnance($ordo);
                $this->entityManager->persist($ordoconsultation);
                $this->entityManager->flush();
                if (isset($rdvRequest["Associer"])){
                    $assoc = new Associer();
                    $assoc->setPraticien($praticien);
                    $assoc->setPatient($patient);
                    $this->entityManager->persist($assoc);
                    $this->entityManager->flush();
                }
            break;

            case 'intervention':
                if ($Id != ''){
                    $inter = $this->intervationConsultationRepository->find($Id);
                }else{
                    $inter = new IntervationConsultation();
                }
                $inter->setPatient($patient);
                $inter->setStatus(0);
                $inter->setEtat(0);
                $inter->setObjetConsultation($description);
                $inter->setOrdonnace($ordo);
                $this->entityManager->persist($inter);
                $this->entityManager->flush();
                if (isset($rdvRequest["Associer"])){
                    $assoc = new Associer();
                    $assoc->setPraticien($praticien);
                    $assoc->setPatient($patient);
                    $this->entityManager->persist($assoc);
                    $this->entityManager->flush();
                }
        }
        $message=$translator->trans('Appointment registration successful');
        $this->addFlash('success', $message);
        return $this->redirectToRoute('rdv_patient');
        }

    /**
     * @Route("/change", name="change")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function change(Request $request, TranslatorInterface $translator){
        $id = $request->request->get('id');
        $type = $request->request->get('type');
        if($type == "consultation"){
            $ordo = $this->ordoConsultationRepository->find($id);
            $ordo->setStatusConsultation(2);
            $this->entityManager->persist($ordo);
            $this->entityManager->flush();
        }elseif ($type == "intervention"){
            $inter = $this->intervationConsultationRepository->find($id);
            $inter->setStatus(2);
            $this->entityManager->persist($inter);
            $this->entityManager->flush();
        }
        $message=$translator->trans('Successful change');
        $this->addFlash('success', $message);
        return new JsonResponse(['status' => 'OK']);

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
     * @Route("/delete-group", name="delete_group")
     */
    public function delete_group(Request $request)
    {
        $user = $this->getUser();
        $idGroup = $request->request->get('id_group');
        if ($idGroup != ""){

            $group_by_family = $this->familyRepository->findBy(['groupFamily' => $idGroup]);
            if($group_by_family && count($group_by_family) >  0){
                foreach ($group_by_family as $family) {
                    $this->entityManager->remove($family);
                }
            }
            $group_family = $this->groupFamilyRepository->find($idGroup);

            if($group_family){
                $this->entityManager->remove($group_family);
            }
            $this->entityManager->flush();
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
            "intervention" =>"INTERVENTION"
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
            if ($ordoCon->getOrdonnance() != null && $ordoCon->getOrdonnance()->getPraticien() != null) $rdv['praticiens'] = $ordoCon->getOrdonnance()->getPraticien();
            if ($rdv['dateRdv'] != ''){
                $date = $rdv['dateRdv']->format('d-m-Y H:i:s');
                $rdv['dateRdv'] = str_replace("-", "/", explode(' ', $date)[0]);
                $rdv['heureRdv'] = explode(' ', $date)[1];
            }

            $form = $this->createForm(RdvType::class, $rdv, ['typeRdvArrays' => $typeRdvArrays]);
            $response = $this->renderView('patient/_form_edit.html.twig', [
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
    public function remove_vaccin(Request $request,TranslatorInterface $translator)
    {
        $Id = $request->request->get('id');
        $type = $request->request->get('type');
        $delete = false;
        if ($type == 'consultation'){
            $ordoCon = $this->ordoConsultationRepository->find($Id);
            if ( $ordoCon != null){
                    $this->entityManager->remove($ordoCon);
                    $this->entityManager->flush();
                    $message = $translator->trans('Appointment has been deleted successfully!');
                    $delete = true;
                    $this->addFlash('success', $message);
            }
        }
        elseif ($type == 'vaccination'){
            $ordoCon = $this->ordoVaccinationRepository->find($Id);
            if ( $ordoCon != null){
                    $this->entityManager->remove($ordoCon);
                    $this->entityManager->flush();
                    $message = $translator->trans('Appointment has been deleted successfully!');
                    $delete = true;
                    $this->addFlash('success', $message);
            }
        }else{
            $intervention = $this->interventionVaccinationRepository->find($Id);
            if ($intervention != null){
                $this->entityManager->remove($intervention);
                $this->entityManager->flush();
                $message = $translator->trans('Appointment has been deleted successfully!');
                $delete = true;
                $this->addFlash('success', $message);
            }

        }
        return new JsonResponse(['form_delete' => $delete]);
    }

    /**
     * @Route("/check-association/{praticien}", name="check_association", defaults={0})
     * @param Request $request
     * @param Praticien $praticien
     * @return JsonResponse
     */
    public function check_association(Request $request, Praticien $praticien)
    {
        $user = $this->getUser();

        $data = 'KO';
        if ($praticien){
            $associate = $this->associerRepository->findOneBy(['patient' => $this->patientRepository->findOneBy(['user'=>$user]), 'praticien' => $praticien]);

            if ($associate != null) $data = 'OK';
        }
        return new JsonResponse(['status' => $data]);
    }
    /**
     *  @Route("/generate", name="generate")
     *
     */
    public function generate(){
        $generation= [];
        $form = $this->createForm(GenerationType::class,$generation);
        return $this->render('patient/_form_generate.html.twig',[
            'new'=> true,
            'form'=>$form->createView(),
            'eventData'=> $generation,
        ]);
    }
    /**
     * @Route("/generation", name="generation")
     * @param Request $request
     * @return Response
     */
    public function  generation(Request $request,TranslatorInterface $translator){
        $Request = $request->request->get("generation");
        $praticien = $request->request->get('praticien');
        $praticien = $this->praticienRepository->find($praticien);
        $ordo = $this->ordonnaceRepository->findOneBy(['praticien' => $praticien]);
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $Id = $Request["id"];
        if ($Id != ''){
            $ordovaccination = $this->ordoVaccinationRepository->find($Id);
        }else{
            $ordovaccination = new OrdoVaccination();
        }
        $ordovaccination->setOrdonnance($ordo);
        $ordovaccination->setPatient($patient);
        $ordovaccination->setStatusVaccin(0);
        $ordovaccination->setEtat(0);
        $this->entityManager->persist($ordovaccination);
        $this->entityManager->flush();

        $message=$translator->trans('registration successful');
        $this->addFlash('success', $message);
        return $this->redirectToRoute('generate');


    }
    /**
     * @Route("/centre", name="centre")
     */
    public function centre(Request $request){
        $id = $request->request->get('id');
        $city= $this->cityRepository->find($id);
        $centre = $this->centreHealthRepository->searchCentre($city);
        return new JsonResponse($centre);
    }
    /**
     * @Route("/p", name="p")
     */
    public function p(Request $request){
        $id = $request->request->get('id');
        $centre= $this->centreHealthRepository->find($id);
        $praticien= $this->ordonnaceRepository->searchPcent($centre);
        return new JsonResponse($praticien);
    }

    /**
     * @Route("/country/fonction", name="fonction_country")
     */
    public function function_country(Request $request)
    {
        $id = $request->request->get('id');
        $fonction = $this->fonctionRepository->find($id);
        $country = $this->fonctionRepository->searchcountry($fonction);
        return new JsonResponse($country);

    }
    /**
     * @Route("/city/fonction", name="fonction_city")
     */
    public function function_city(Request $request)
    {
        $id = $request->request->get('id');
        $state = $request->request->get('state');
        $fonction = $this->fonctionRepository->find($id);
        $state= $this->stateRepository->find($state);
        $country = $this->fonctionRepository->searchcity($fonction,$state);
        return new JsonResponse($country);

    }

    /**
     * @Route("/praticien/fonction", name="fonction_praticien")
     */
    public function function_praticien(Request $request)
    {
        $id = $request->request->get('id');
        $state = $request->request->get('state');
        $city= $request->request->get('city');
        $fonction = $this->fonctionRepository->find($id);
        $state= $this->stateRepository->find($state);
        $city = $this->cityRepository->find($city);
        $country = $this->fonctionRepository->searchpraticien($fonction,$state,$city);
        return new JsonResponse($country);

    }
    /**
     * @Route("/generation/vaccin", name="generation_vaccin")
     */
    public function generer(){
        $user = $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $carnet = $this->carnetVaccinationRepository->searchCarnet($patient);
        $data = 0;
        if($carnet != null) $data = 1;
        return new JsonResponse($data);
    }






}
