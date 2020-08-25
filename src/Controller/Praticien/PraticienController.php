<?php

namespace App\Controller\Praticien;

use App\Entity\CarnetVaccination;
use App\Entity\IntervationConsultation;
use App\Entity\InterventionVaccination;
use App\Entity\OrdoConsultation;
use App\Entity\OrdoVaccination;
use App\Entity\PatientIntervationConsultation;
use App\Entity\PropositionRdv;
use App\Form\CarnetType;
use App\Form\ConsultationPraticienType;
use App\Form\PropositionRdvType;
use App\Repository\FamilyRepository;
use App\Repository\CarnetVaccinationRepository;
use App\Repository\IntervationConsultationRepository;
use App\Repository\InterventionVaccinationRepository;
use App\Repository\OrdoConsultationRepository;
use App\Repository\OrdoVaccinationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\PropositionRdvRepository;
use App\Repository\VaccinRepository;
use App\Service\VaccinGenerate;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
/**
 * @Route("/praticien")
 */
class PraticienController extends AbstractController
{
    protected $vaccinGenerate;
    protected $patientRepository;
    protected $praticienRepository;
    protected $familyRepository;
    protected $entityManager;
    protected $ordoConsultationRepository;
    protected $ordoVaccinationRepository;
    protected $vaccinRepository;
    protected $intervationConsultationRepository;
    protected $interventionVaccinationRepository;
    protected $propositionRdvRepository;
    protected $carnetVaccinationRepository;



    function __construct(
        VaccinGenerate $vaccinGenerate,
        PatientRepository $patientRepository,
        PropositionRdvRepository $propositionRdvRepository,
        CarnetVaccinationRepository $carnetVaccinationRepository,
        PraticienRepository $praticienRepository,
        FamilyRepository $familyRepository,
        OrdoConsultationRepository $ordoConsultationRepository,
        OrdoVaccinationRepository $ordoVaccinationRepository,
        IntervationConsultationRepository $intervationConsultationRepository,
        InterventionVaccinationRepository $interventionVaccinationRepository,
        EntityManagerInterface $entityManager,
        VaccinRepository $vaccinRepository
    )
    {
        $this->vaccinGenerate = $vaccinGenerate;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->carnetVaccinationRepository=$carnetVaccinationRepository;
        $this->familyRepository = $familyRepository;
        $this->propositionRdvRepository= $propositionRdvRepository;
        $this->entityManager = $entityManager;
        $this->ordoConsultationRepository = $ordoConsultationRepository;
        $this->ordoVaccinationRepository = $ordoVaccinationRepository;
        $this->intervationConsultationRepository=$intervationConsultationRepository;
        $this->interventionVaccinationRepository=$interventionVaccinationRepository;
        $this->vaccinRepository = $vaccinRepository;
    }
    /**
     * @Route("/", name="praticien")
     */
    public function praticien()
    {

        if (!$this->isGranted('ROLE_PRATICIEN')) {
            return $this->redirectToRoute('homepage');
        }
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $rvc = $this->ordoVaccinationRepository->searchStatusPraticien($praticien->getId());

        // return $this->render('praticien/vaccination.html.twig', [
        //     'vaccination' => $rvc,
        // ]);
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/consultation", name="consultaion_praticien")
     */
    public function consultaion_praticien()
    {
        if (!$this->isGranted('ROLE_PRATICIEN')) {
            return $this->redirectToRoute('homepage');
        }
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $rvc = $this->ordoConsultationRepository->searchStatusPraticien($praticien->getId());
        $pr = $this->propositionRdvRepository->searchStatusPraticien($praticien->getId());

        return $this->render('praticien/consultation.html.twig', [
            'consultation' => $rvc,
            'proposition'=>$pr,
        ]);
    }

    /**
     * @Route("/vaccination", name="vaccination_praticien")
     */
    public function vaccination_patient()
    {
        if (!$this->isGranted('ROLE_PRATICIEN')) {
            return $this->redirectToRoute('homepage');
        }
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $rvc = $this->ordoVaccinationRepository->searchStatusPraticien($praticien->getId() );

        return $this->render('praticien/vaccination.html.twig', [
            'vaccination' => $rvc,
        ]);
    }
    /**
     * @Route("/intervention", name="intervention_praticien")
     */
    public function intervention_active()
    {
        $user =$this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $icp= $this->intervationConsultationRepository->searchIntervationPraticien($praticien->getId());
        $ivp= $this->interventionVaccinationRepository->searchIntervationPraticien($praticien->getId());
        return $this->render('praticien/intervention.html.twig', [
            'consultation'=>$icp,
            'vaccination'=>$ivp,

        ]);
    }

    

    /**
     * @Route("/intervention/rejected", name="intervention_praticien_reject")
     */
    public function intervention_reject()
    {
        $user =$this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $icp= $this->intervationConsultationRepository->searchIntervationPraticien($praticien->getId());
        $ivp= $this->interventionVaccinationRepository->searchIntervationPraticien($praticien->getId());
        return $this->render('praticien/intervention_reject.html.twig', [
            'consultation'=>$icp,
            'vaccination'=>$ivp,

        ]);

    }

    /**
     * @Route("/rdv-prat/rejected", name="rdv_praticien_reject")
     *
     */
    public function rdv_praticien_reject()
    {
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $rce = $this->ordoConsultationRepository->searchStatusPraticien($praticien->getId());
        $rve = $this->ordoVaccinationRepository->searchStatusPraticien($praticien->getId());
        return $this->render('praticien/rdv_annuler_patient.html.twig',[
            'consultation'=> $rce,
            'vaccination'=>$rve
        ]);
    }


    /**
     * @Route("/rdv-prat", name="rdv_praticien")
     */
    public function rdv_praticien()
    {
        if (!$this->isGranted('ROLE_PRATICIEN')) {
            return $this->redirectToRoute('homepage');
        }
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $rce = $this->ordoConsultationRepository->searchStatusPraticienEnValid($praticien->getId());
        dump($praticien);
        $rve = $this->ordoVaccinationRepository->searchStatusPraticienEnValid($praticien->getId());


        return $this->render('praticien/rdv.html.twig', [
            'consultation'=>$rce,
            'vaccination'=>$rve,
        ]);
    }

    /**
     * @Route("/see-calendar/{patient_id}", name="see_calendar")
     */
    public function see_calendar(
      Request $request, $patient_id, 
      VaccinRepository $vacRepo, 
      PatientRepository $patientRepo
    ){
        $user= $this->getUser();

        $patient = $patientRepo->find($patient_id);

        $typePatient = $patient->getTypePatient();
        $carnetRepo = $this->getDoctrine()->getRepository(CarnetVaccination::class);
        // $listVaccins = $carnetRepo->findBy(['patient' => $patient]);
        $listVaccins = $carnetRepo->findListVaccinsInCarnet($patient);

        return $this->render("praticien/carnet.html.twig",[
          'patient' => $patient,
          'listVaccins' => $listVaccins
        ]);
    }

    /**
     * @Route("/update/edit", name="change_status")
     */
      public function  update( Request $request, TranslatorInterface $translator, VaccinGenerate $vaccGen)
      {
            $id= $request->request->get('id');
            $prat = $request->request->get('praticien');
            $praticien=$this->praticienRepository->find($prat);
            $pat = $request->request->get('patient');
            $patient =  $this->patientRepository->find($pat);
            $date = $request->request->get('date');
            $Date_Rdv = new \DateTime($date);
            $action = $request->request->get('action');
            $type = $request->request->get('type');
            switch ($action){
                case 'active':
                    if($type == "consultation"){
                        $ordoConsu = $this->ordoConsultationRepository->find($id);
                        if($ordoConsu != null){
                            $interConsu = new IntervationConsultation();
                            $interConsu->setPatient($patient);
                            $interConsu->setPraticienPrescripteur($praticien);
                            $interConsu->setDateConsultation( $Date_Rdv);
                            $interConsu->setOrdoConsulataion($ordoConsu);
                            $interConsu->setPraticienConsultant($praticien);
                            $interConsu->setEtat(0);
                            $interConsu->getOrdoConsulataion()->setStatusConsultation(1);
                            $interConsu->getOrdoConsulataion()->setStatusNotif(1);
                            $this->entityManager->persist($interConsu);
                            $this->entityManager->flush();
                            $patientintervationconsultation = new PatientIntervationConsultation();
                            $patientintervationconsultation->setPatient($patient);
                            $patientintervationconsultation->setInterventionConsultation($interConsu);
                            $this->entityManager->persist($patientintervationconsultation);
                            $this->entityManager->flush();
                        }
                    }else{
                        $ordoVacc = $this->ordoVaccinationRepository->find($id);
                        if($ordoVacc != null){
                            $interVacc = new  InterventionVaccination();
                            $interVacc->setPatient($patient);
                            $interVacc->setPraticienPrescripteur($praticien);
                            $interVacc->setEtat(0);
                            $interVacc->setDatePriseVaccin( $Date_Rdv);
                            $interVacc->setPraticienExecutant($praticien);
                            $interVacc->setOrdoVaccination($ordoVacc);
                            $interVacc->getOrdoVaccination()->setStatusVaccin(1);
                            $interVacc->getOrdoVaccination()->setStatusNotif(1);
                            $this->entityManager->persist($interVacc);
                            $this->entityManager->flush();
                            $state = $patient->getAddressOnBorn()->getRegion()->getState()->getNameState();
                            $birthday = $patient->getDateOnBorn();
                            $type_patient = $patient->getTypePatient();

                            switch($type_patient){
                                case 'ENFANT':
                                    $alls = $this->vaccinRepository->findVaccinByTYpe('ENFANT', $state);
                                    break;
                                case 'ADULTE':
                                    $alls = $this->vaccinRepository->findVaccinByTYpe('ADULTE');
                                    break;
                                case 'FEMME ENCEINTE':
                                    $alls = $this->vaccinRepository->findVaccinByTYpe('FEMME ENCEINTE');
                                    break;
                            }
                            $this->vaccinGenerate->generate_vaccin($patient, $birthday, $alls, $interVacc);
                        }
                    }
                    $message=$translator->trans('Successful change');
                    $this->addFlash('success', $message);
                    return new JsonResponse(['status' => 'OK']);
                    break;

                case 'reject':
                    if ($type == "consultation"){
                        $ordoConsu = $this->ordoConsultationRepository->find($id);
                        if($ordoConsu != null){
                            $ordoConsu->setStatusConsultation(2);
                            $ordoConsu->setStatusNotif(1);
                            $this->entityManager->persist($ordoConsu);
                            $this->entityManager->flush();
                        }
                    }else{
                        $ordoVacc = $this->ordoVaccinationRepository->find($id);
                        if($ordoVacc != null){
                            $ordoVacc->setStatusVaccin(2);
                            $ordoVacc->setStatusNotif(1);
                            $this->entityManager->persist($ordoVacc);
                            $this->entityManager->flush();
                        }
                    }
                    $message=$translator->trans('Successful change');
                    $this->addFlash('success', $message);
                    return new JsonResponse(['status' => 'OK']);
                    break;
            }
      }

    /**
     * @Route("/update/etat", name="update_etat")
     */
       public function update_etat(Request $request, TranslatorInterface $translator)
           {
               $Id= $request->request->get('id');
               $propos =  $request->request->get('proposition');
               $type = $request->request->get('type');
               switch ($type) {
                   case "vaccination":
                       $intervention = $this->interventionVaccinationRepository->find($Id);
                       if ($intervention != null) {
                           $intervention->getOrdoVaccination()->setEtat(1);
                           $intervention->setEtat(1);
                           $this->entityManager->persist($intervention);
                           $this->entityManager->flush();
                       }
                       break;
                   case "consultation":
                       $inter = $this->intervationConsultationRepository->find($Id);
                       $proposition = $this->propositionRdvRepository->find($propos);
                       if ($proposition  != null) {
                           $inter->getProposition()->setEtat(1);
                       }else{
                           $inter->getOrdoConsulataion()->setEtat(1);
                       }
                       $inter->setEtat(1);
                       $this->entityManager->persist($inter);
                       $this->entityManager->flush();
                       break;
               }
               $message=$translator->trans('Successful change');
               $this->addFlash('success', $message);
               return new JsonResponse(['status' => 'OK']);
           }

    /**
     * @Route("/form-add", name="add_form_rdv")
     */
    public function add_form_rdv(Request $request)
    {

        $action = $request->request->get('action');
        $eventData = [];
        $eventData['type_id'] = 2;
        $form = $this->createForm(ConsultationPraticienType::class, $eventData);

        if ($action == "new") {
            $response = $this->renderView('praticien/modal/new_consultation.html.twig', [
                'new' => true,
                'form' => $form->createView(),
                'eventData' => $eventData,
            ]);
        } else {

            $response = $this->renderView('praticien/modal/new_consultation.html.twig', [
                'new' => false,
                'form' => $form->createView(),
                'eventData' => $eventData,
            ]);
        }
        $form->handleRequest($request);
        return new JsonResponse(['form_consultation_html' => $response]);
    }


    /**
     * @Route("/form-add-proposition", name="add_form_proposition_rdv", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @return JsonResponse
     */
    public function add_form_proposition (Request $request)
    {
        $action = $request->request->get('action');
        $rdv = [];
        if ($action == "new") {
            $form = $this->createForm(PropositionRdvType::class, $rdv);
            $response = $this->renderView('praticien/_form_proposition.html.twig', [
                'new' => true,
                'form' => $form->createView(),
                'eventData' => $rdv,
            ]);
        } else {
            $action = $request->request->get('id');
            $rdv['id'] = $request->request->get('id');
            $propos = $this->propositionRdvRepository->find($rdv['id']);
            $rdv['description'] = $propos->getDescriptionProposition();
            $rdv['dateRdv'] = $propos->getDateProposition();
            if ($rdv['dateRdv'] != ''){
                $date = $rdv['dateRdv']->format('d-m-Y H:i:s');
                $rdv['dateRdv'] = str_replace("-", "/", explode(' ', $date)[0]);
                $rdv['heureRdv'] = explode(' ', $date)[1];
            }
            $form = $this->createForm(PropositionRdvType::class, $rdv);
            $response = $this->renderView('praticien/_form_proposition.html.twig', [
                'new' => false,
                'form' => $form->createView(),
                'eventData' => $rdv,
            ]);
        }
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);
    }

    /**
     * @Route("/proposition/in", name="rdv_proposition")
     */
    public  function rdv_proposition()
    {
        $user= $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $prop = $this->propositionRdvRepository->searchStatusPraticienEnValid($praticien);
        return $this->render('praticien/proposition.html.twig',[
            'proposition'=>$prop,
        ]);
    }

    /**
     * @Route("/register_proposition", name ="register_proposition")
     */

    public  function register_proposition(Request $request)
    {
        $propositionRequest=$request->request->get("proposition_rdv");
        $description = $propositionRequest["description"];
        $patient = $propositionRequest["patient"];
        $date =$propositionRequest["dateRdv"];
        $heure = $propositionRequest["heureRdv"];
        $Id = $propositionRequest["id"];

            $user = $this->getUser();
        $rdv_date = str_replace("/", "-", $date);
        $Date_Rdv = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$heure)));
        $praticien= $this->praticienRepository->findOneBy(['user'=>$user]);
        if($patient != ''){
            $patient =  $this->patientRepository->find($patient);

        }
        if($Id !='')
        {
            $proposition = $this->propositionRdvRepository->find($Id);
        }else {
            $proposition= new PropositionRdv();
        }
        $proposition->setDescriptionProposition($description);
        $proposition->setDateProposition($Date_Rdv);
        $proposition->setPraticien($praticien);
        $proposition->setPatient($patient);
        $proposition->setStatusProposition(0);
        $proposition->setEtat(0);
        $proposition->setStatusNotif(0);
        $this->entityManager->persist($proposition);
        $this->entityManager->flush();
        return $this->redirectToRoute('rdv_proposition');
    }

    /**
     * @Route("/proposition/remove", name="remove_proposition", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_proposition(Request $request, TranslatorInterface $translator)
    {
            $Id = $request->request->get('id');
            $propos = $this->propositionRdvRepository->find($Id);
            $this->entityManager->remove($propos);
            $this->entityManager->flush();
            $delete = true;
            $message=$translator->trans('The Appointment proposal has been successfully deleted!');
            $this->addFlash('success', $message);
            return new JsonResponse(['form_delete' => $delete]);
    }

    /**
     * @Route("/dashboard", name ="dashboard")
     */
    public function dashboard()
    {
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $patientCons = $this->intervationConsultationRepository->searchPatient($praticien);
        $patientVacc = $this->interventionVaccinationRepository->searchPatient($praticien);

        // Get Number of both Unrealized and Realized Vaccination
        $nbUnrealizedVacc = $this->interventionVaccinationRepository->countUnrealizedVacc($praticien);
        $nbRealizedVacc = $this->interventionVaccinationRepository->countRealizedVacc($praticien);

        foreach ($patientCons as $patientt){
          foreach ($patientVacc as $patient){
            $patientv = $patient[1];
            $patientc = $patientt[1];
            $patient = $patientv + $patientc;
          }
        }
        return $this->render('praticien/dashboard.html.twig', [
            "nbPatient"=>$patient,
            "nbUnrealizedVacc" => $nbUnrealizedVacc[0][1],
            "nbRealizedVacc" => $nbRealizedVacc[0][1],
            "nbConsultation" => $patientCons[0][1]
        ]);
    }

    /**
    * @Route("/chart/nb_prise_type_vacc", name="chart/nb_prise_type_vacc"), methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
    */
    public function nb_prise_type_vacc(){
      $userId = $this->getUser()->getId();
      $queryResult = $this->vaccinRepository->countPriseVaccinParType($userId);
      $result = [];
      foreach($queryResult as $res){
        array_push($result, array(
          "label" => $res["typeVaccin"],
          "y" => intval($res["nb"])
        ));
      }

      return new JsonResponse($result);
    }

    /**
    * @Route("/chart/age_range", name="age_range")
    */
    public function age_range(){
      $userId = $this->getUser()->getId();
      $patientsBirthday = $this->ordoVaccinationRepository->findPatientsBirthday($userId);
      $patientsAgeRange = array();
      // Count each range of 10
      foreach($patientsBirthday as $birthday){
        for($i=10; $i<=100; $i+=10){
          if(intval($birthday["birthday"]->diff(new \Datetime())->format("%y")) < $i && intval($birthday["birthday"]->diff(new \Datetime())->format("%y")) >= $i-10){
            if(array_key_exists((string)$i-10 . " à " . (string)($i-1), $patientsAgeRange)){
              $patientsAgeRange[(string)$i-10 . " à " . (string)($i-1)]++;
            }
            else{
              $patientsAgeRange[(string)$i-10 . " à " . (string)($i-1)] = 1;
            }
          }
          else{
            if(!array_key_exists((string)$i-10 . " à " . (string)($i-1), $patientsAgeRange)){
              $patientsAgeRange[(string)$i-10 . " à " . (string)($i-1)] = 0;
            }
          }
        }
      }
      $result = [];
      foreach ($patientsAgeRange as $key => $value) {
        $age = [];
        $age["label"] = $key;
        $age["y"] = $value;
        array_push($result, $age);
      }

      return new JsonResponse($result);
    }

    /**
    * @Route("/chart/vaccin_stat", name="/chart/vaccin_stat")
    */
    public function vaccin_stat(){
        $userId = $this->getUser()->getId();

        $queryResult = $this->carnetVaccinationRepository->findvaccin($userId);
        $result = [];

            foreach($queryResult as $res){
                array_push($result, array(
                    "label" => $res["vaccin"],
                    "y" => intval($res["patient"])
                ));
            }


      return new JsonResponse($result);
    }


    /**
     * @Route("/update/carnet", name="update_carnet")
     */
    public function update_carnet(Request $request, TranslatorInterface $translator)
    {
        $carnet = $this->carnetVaccinationRepository->find($request->request->get('id'));
        $carnet->setEtat(true);
        $this->entityManager->persist($carnet);
        $this->entityManager->flush();
        $message=$translator->trans('Successful change');
        $this->addFlash('success', $message);
        return new JsonResponse(['status' => 'OK']);
    }

    /**
     * @Route("/form-show-edit-carnet", name="show_edit_carnet")
     */
    public function show_edit_carnet(Request $request)
    {

        $id = $request->request->get('id');
        $eventData = [];
        $carnetVacc = $this->carnetVaccinationRepository->find($id);
        $eventData['id'] = $carnetVacc->getId();
        if ($carnetVacc->getRappelVaccin() != null){
            $eventData['type'] = 'rpv';
            $eventData['dateCarnet'] = Carbon::parse($carnetVacc->getRappelVaccin())->format('d/m/Y');
            $eventData['timeCarnet'] = Carbon::parse($carnetVacc->getRappelVaccin())->format('H:m:s');
        }else {
            $eventData['type'] = 'dti';
            $eventData['dateCarnet'] = Carbon::parse($carnetVacc->getDatePriseInitiale())->format('d/m/Y');
            $eventData['timeCarnet'] = Carbon::parse($carnetVacc->getDatePriseInitiale())->format('H:m');
        }
        $form = $this->createForm(CarnetType::class, $carnetVacc);


        $response = $this->renderView('praticien/modal/edit_carnet_vaccin.html.twig', [
            'form' => $form->createView(),
            'eventData' => $eventData,
        ]);

        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);
    }

    /**
     * @Route("/edit/carnet-vaccination", name ="edit_carnet_vaccination")
     */

    public  function edit_carnet_vaccination(Request $request)
    {

        $id = $request->request->get('id');
        $type= $request->request->get('type');
        $time_carnet = $request->request->get('time_carnet');

        $carnetVacc = $this->carnetVaccinationRepository->find($id);
        if ($type == 'rpv') {
            $rappelVaccin = $request->request->get('rappelVaccin');
            $rdv_date = str_replace("/", "-", $rappelVaccin);
            $Date_Carnet = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$time_carnet)));
            $carnetVacc->setRappelVaccin($Date_Carnet);
        } elseif ($type == 'dti'){
            $datePriseInitiale = $request->request->get('datePriseInitiale');
            $rdv_date = str_replace("/", "-", $datePriseInitiale);
            $Date_Carnet = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$time_carnet)));
            $carnetVacc->setDatePriseInitiale($Date_Carnet);
        }

        $this->entityManager->persist($carnetVacc);
        $this->entityManager->flush();
        return $this->redirect($this->generateUrl("see_calendar", array('patient_id' => $carnetVacc->getPatient()->getId())));

    }
}
