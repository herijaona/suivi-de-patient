<?php

namespace App\Controller\Praticien;

use App\Entity\CarnetVaccination;
use App\Entity\IntervationConsultation;
use App\Entity\InterventionVaccination;
use App\Entity\OrdoConsultation;
use App\Entity\OrdoVaccination;
use App\Entity\PropositionRdv;
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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
// use App\Service\VaccinGenerate;

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



    function __construct(
        VaccinGenerate $vaccinGenerate,
        PatientRepository $patientRepository,
        PropositionRdvRepository $propositionRdvRepository,
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

        return $this->render('praticien/vaccination.html.twig', [
            'vaccination' => $rvc,
        ]);
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

        return $this->render('praticien/consultation.html.twig', [
            'consultation' => $rvc,
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
            $praticien = $request->request->get('praticien');
            $patient = $request->request->get('patient');
            $date = $request->request->get('date');
            $patient =  $this->patientRepository->find($patient);
            $praticien=$this->praticienRepository->find($praticien);
            $ordoconsu=$this->ordoConsultationRepository->find($id);
            $ordovacc=$this->ordoVaccinationRepository->find($id);
            $Date_Rdv = new \DateTime($date);
          if($request->request->get('action')== "active"){
              if($request->request->get('type') == "consultation" && $request->request->get('etat') == 0){
                  $ordoConsu = $this->ordoConsultationRepository->find($request->request->get('id'));
                  if($ordoConsu != null){
                      $interConsu = new IntervationConsultation();
                      $interConsu->setPatient($patient);
                      $interConsu->setPraticienPrescripteur($praticien);
                      $interConsu->setDateConsultation( $Date_Rdv);
                      $interConsu->setOrdoConsulataion($ordoconsu);
                      $interConsu->setPraticienConsultant($praticien);
                      $interConsu->setEtat(0);
                      $this->entityManager->persist($interConsu);
                      $this->entityManager->flush();
                      $ordoConsu->setStatusConsultation(1);
                      $this->entityManager->persist($ordoConsu);
                      $this->entityManager->flush();
                  }
              }elseif($request->request->get('type') == "vaccination" && $request->request->get('etat') == 0){
                  $ordoVacc = $this->ordoVaccinationRepository->find($request->request->get('id'));
                  if($ordoVacc != null){
                      $interVacc = new  InterventionVaccination();
                      $interVacc->setPatient($patient);
                      $interVacc->setPraticienPrescripteur($praticien);
                      $interVacc->setEtat(0);
                      $interVacc->setDatePriseVaccin( $Date_Rdv);
                      $interVacc->setPraticienExecutant($praticien);
                      $interVacc->setOrdoVaccination($ordovacc);
                      $this->entityManager->persist($interVacc);
                      $this->entityManager->flush();
                      $ordoVacc->setStatusVaccin(1);
                      $this->entityManager->persist($ordoVacc);
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
          }elseif ($request->request->get('action')== "reject"){
              if($request->request->get('type') == "consultation" && $request->request->get('etat') == 0){
                  $ordoConsu = $this->ordoConsultationRepository->find($request->request->get('id'));
                  if($ordoConsu != null){
                      $interCons = new IntervationConsultation();
                      $interCons->setPatient($patient);
                      $interCons->setPraticienPrescripteur($praticien);
                      $interCons->setDateConsultation( $Date_Rdv);
                      $interCons->setOrdoConsulataion($ordoconsu);
                      $interCons->setPraticienConsultant($praticien);
                      $interCons->setEtat(0);
                      $this->entityManager->persist($interCons);
                      $this->entityManager->flush();
                      $ordoConsu->setStatusConsultation(1);
                      $this->entityManager->persist($ordoConsu);
                      $this->entityManager->flush();
                  }
              }
              elseif($request->request->get('type') == "vaccination" && $request->request->get('etat') == 0){
                  $ordoVacc = $this->ordoVaccinationRepository->find($request->request->get('id'));
                  $vaccin = $request->request->get('vaccin');
                  $vaccination= $this->vaccinRepository->find($vaccin);
                  if($ordoVacc != null){
                      $interVacc = new  InterventionVaccination();
                      $interVacc->setPatient($patient);
                      $interVacc->setPraticienPrescripteur($praticien);
                      $interVacc->setEtat(0);
                      $interVacc->setVaccin($vaccination);
                      $interVacc->setDatePriseVaccin( $Date_Rdv);
                      $interVacc->setPraticienExecutant($praticien);
                      $interVacc->setOrdoVaccination($ordovacc);
                      $this->entityManager->persist($interVacc);
                      $this->entityManager->flush();
                      $ordoVacc->setStatusVaccin(1);
                      $this->entityManager->persist($ordoVacc);
                      $this->entityManager->flush();

                      // Get list of vaccination->rappel() methods
                      $vaccMethods = get_class_methods($vaccination);

                      // Add new line in CarnetVaccin foreach rappel of vaccin
                      foreach($vaccMethods as $getRappel) {

                          // If $getRappel contains "getRappel" in its value
                          if (strpos($getRappel, "getRappel") !== false) {

                              $rappel = $vaccination->$getRappel();

                              if ($rappel !== "" && $rappel !== null) {
                                  $carnetVaccination = new CarnetVaccination();

                                  $carnetVaccination->setIntervationVaccination($interVacc)
                                      ->setPatient($patient)
                                      ->setVaccin($vaccination)
                                      ->setEtat(1);

                                  $rappel = new \DateTime(date('Y-m-d H:i:s', strtotime($rappel)));

                                  $carnetVaccination->setRappelVaccin($rappel);

                                  $this->entityManager->persist($carnetVaccination);
                                  $this->entityManager->flush();
                              }
                          }
                      }
                  }
              }
          }
          $message=$translator->trans('Successful change');
          $this->addFlash('success', $message);
          return new JsonResponse(['status' => 'OK']);
      }

    /**
     * @Route("/update/etat", name="update_etat")
     */
       public function update_etat(Request $request, TranslatorInterface $translator)
           {
               $ordoVacc = $this->ordoVaccinationRepository->find($request->request->get('id'));
               $ordoCons = $this->ordoConsultationRepository->find($request->request->get('id'));
               if($request->request->get('action') == "active")
               {
                   if($request->request->get('type') == "vaccination" && $request->request->get('etat') == 0){
                       $intervention = $this->interventionVaccinationRepository->find($request->request->get('id'));

                       if($intervention != null){
                           $intervention->setEtat(1);
                           $this->entityManager->persist($intervention);
                           $this->entityManager->flush();
                           $ordoVacc->setEtat(1);
                           $this->entityManager->persist($ordoVacc);
                           $this->entityManager->flush();

                       }
                   }else{
                       $inter = $this->intervationConsultationRepository->find($request->request->get('id'));

                       if($inter != null){
                           $inter->setEtat(1);
                           $this->entityManager->persist($inter);
                           $this->entityManager->flush();
                           $ordoCons->setEtat(1);
                           $this->entityManager->persist($ordoCons);
                           $this->entityManager->flush();


                       }
                   }

               }
               $message=$translator->trans('Successful change');
               $this->addFlash('success', $message);
               return new JsonResponse(['status' => 'OK']);
           }

    /**
     * @Route("/update-rdv", name="update_etat_rdv")
     */
    public function update_etat_rdv(Request $request, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        //$Rdv = $this->rendezVousRepository->find($request->request->get("id_rdv"));
        $Rdv = null;
        if ($Rdv){
            if ($Rdv->getPraticien() == null){
                $Rdv->setPraticien($praticien);
            }
            if ($request->request->get("etat") == 0){
                $Rdv->setEtat(true);
            }elseif ($request->request->get("etat") == 1){
                $Rdv->setEtat(false);
            }
        }
        $this->entityManager->persist($Rdv);
        $this->entityManager->flush();
        //$message=$translator->trans('Successful change');
        //$this->addFlash('success', $message);
        return new JsonResponse(['status' => 'OK']);
    }

    /**
     * @Route("/update-rdv-status", name="update_status_rdv")
     */
    public function update_status_rdv(Request $request, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        //$Rdv = $this->rendezVousRepository->find($request->request->get("id_rdv"));
        $Rdv = null;
        if ($Rdv && $Rdv->getPraticien() != null && $Rdv->getPraticien()->getId() == $praticien->getId()){
            if ($request->request->get("status") == 0){
                $Rdv->setStatus(1);
            }elseif ($request->request->get("status") == 1){
                $Rdv->setStatus(0);
            }
            $this->entityManager->persist($Rdv);
            $this->entityManager->flush();
            //$message=$translator->trans('Successful change');
            //$this->addFlash('success', $message);

        }else{
            //$message=$translator->trans('You do not have access to this event');
            //$this->addFlash('error', $message);
        }
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
     * @Route("/register-rdv-praticien", name="register_rdv_praticien")
     */
    public function register_rdv_praticien(Request $request)
    {

        $consultation_praticien = $request->request->get("consultation_praticien");
        $type_id = $consultation_praticien['type_id'];
        $date = $consultation_praticien['date_consultation'];
        $description = $consultation_praticien['description'];
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $rdv_date = str_replace("/", "-", $date);

        $Date_Rdv = new \DateTime(date ("Y-m-d", strtotime ($rdv_date)));

        /*$rdv = new RendezVous();
        $rdv->setPraticien($praticien);
        $rdv->setDescription($description);
        $rdv->setType($type_id);
        $rdv->setDatePriseInitiale($Date_Rdv);
        $rdv->setPatient(null);
        $rdv->setVaccin(null);
        $this->entityManager->persist($rdv);
        $this->entityManager->flush();*/
        if ($type_id == 1 ){
            return $this->redirectToRoute('vaccination_praticien');
        }elseif ($type_id == 2 ){
            return $this->redirectToRoute('consultaion_praticien');
        }elseif ($type_id == 3){
            return $this->redirectToRoute('rdv_praticien');
        }

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
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($proposition);
        $entityManager->flush();
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
     * @Route("/notification" , name ="notif")
     */
    public function notif( Request $request)
    {
        $user= $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $cons= $this->ordoConsultationRepository->searchStatusPraticienNotif($praticien);
        $co= $this->ordoConsultationRepository->searchStatusPraticienAll($praticien);
        $vacc = $this->ordoVaccinationRepository->searchStatusPraticienNotif($praticien);
        $vac = $this->ordoVaccinationRepository->searchStatusPraticienAll($praticien);

        $consultation ='';
        $vaccination ='';
        foreach ($vacc as $row){
            $te = $row[1];
            foreach ($vac as $noti) {
                $nom = $noti["lastName"];
                $prenom = $noti["firstName"];
                $vaccination .='
           <li class="dropdown-item" style="width: 100%; ">
           <a href="rdv-prat">
           <strong> Demande de Vaccination </strong><br/>
           <small><em>'.$nom.' a envoyé demande vaccination </em></small>
           </a>
           </li>
           ';

            }

        }
        foreach ($cons as $rows){
       foreach ($co as $notif){
           $tes = $rows[1];
           $nom = $notif["lastName"];
           $prenom = $notif["firstName"];
           $consultation .='
           <li class="dropdown-item" style="width: 100%; ">
           <a href="rdv-prat">
           <strong> Demande de Consultation </strong><br/>
           <small><em>'.$nom.' a envoyé demande consultation </em></small>
           </a>
           </li>
           ';
       }
    }



        $count= $tes + $te;
        $notifig= $consultation . $vaccination;


        return new JsonResponse(['unseen_notification'=>$count,'notification'=>$notifig]);

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

      // Get the current user/praticien patients birthday
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
    * @Route("/chart/vaccin_stat", name="vaccin_stat")
    */
    public function vaccin_stat(){
      $userId = $this->getUser()->getId();

      $queryResult = $this->vaccinRepository->getVaccStat($userId);
      return new JsonResponse($queryResult);
    }
}
