<?php

namespace App\Controller\Praticien;

use App\Entity\Associer;
use App\Entity\CarnetVaccination;

use App\Entity\IntervationConsultation;
use App\Entity\InterventionVaccination;
use App\Entity\OrdoConsultation;
use App\Entity\PropositionRdv;
use App\Form\AcceptType;
use App\Form\CarnetType;
use App\Form\ConsultationPraticienType;
use App\Form\GenerationVaccinType;
use App\Form\PropositionRdvType;
use App\Form\RdvAssocieType;
use App\Form\RdvType;
use App\Repository\AssocierRepository;
use App\Repository\FamilyRepository;
use App\Repository\CarnetVaccinationRepository;
use App\Repository\IntervationConsultationRepository;
use App\Repository\InterventionVaccinationRepository;
use App\Repository\OrdoConsultationRepository;
use App\Repository\OrdonnaceRepository;
use App\Repository\OrdoVaccinationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\PropositionRdvRepository;
use App\Repository\VaccinRepository;
use App\Service\VaccinGenerate;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    protected $associerRepository;
    protected $ordonnaceRepository;



    function __construct(
        VaccinGenerate $vaccinGenerate,
        PatientRepository $patientRepository,
        PropositionRdvRepository $propositionRdvRepository,
        CarnetVaccinationRepository $carnetVaccinationRepository,
        PraticienRepository $praticienRepository,
        FamilyRepository $familyRepository,
        AssocierRepository $associerRepository,
        OrdonnaceRepository $ordonnaceRepository,
        OrdoConsultationRepository $ordoConsultationRepository,
        OrdoVaccinationRepository $ordoVaccinationRepository,
        IntervationConsultationRepository $intervationConsultationRepository,
        InterventionVaccinationRepository $interventionVaccinationRepository,
        EntityManagerInterface $entityManager,
        VaccinRepository $vaccinRepository
    )
    {
        $this->ordonnaceRepository=$ordonnaceRepository;
        $this->vaccinGenerate = $vaccinGenerate;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->carnetVaccinationRepository=$carnetVaccinationRepository;
        $this->familyRepository = $familyRepository;
        $this->propositionRdvRepository= $propositionRdvRepository;
        $this->associerRepository= $associerRepository;
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
        $rvc = $this->ordoVaccinationRepository->searchStatusPraticien($praticien->getId());
        $intervention = $this->interventionVaccinationRepository->searchIntCarnet($praticien);
        return $this->render('praticien/vaccination.html.twig', [
            'vaccination' => $rvc,
            'intervention'=> $intervention,
        ]);
    }
    /**
     * @Route("/intervention", name="intervention_praticien")
     */
    public function intervention_active()
    {
        $user =$this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $ivp= $this->interventionVaccinationRepository->searchIntervationPraticien($praticien->getId());
        $proposition = $this->propositionRdvRepository->searchSta($praticien->getId());
        return $this->render('praticien/intervention.html.twig', [
            'vaccination'=>$ivp,
            'proposition'=>$proposition,
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
     * @Route("/rdv/in", name="rdv_praticien")
     */
    public function rdv_praticien()
    {
        $user = $this->getUser();
        $praticien= $this->praticienRepository->findOneBy(['user'=>$user]);
        $ordo = $this->ordoConsultationRepository->searchStatusPraticien($praticien);
        $intervention = $this->intervationConsultationRepository->searchIn($praticien);
        return $this->render('praticien/rdv_praticien.html.twig', [
            'consultation'=>$ordo,
            'intervention'=>$intervention,

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
        $carnetRepo = $this->carnetVaccinationRepository;
        // $listVaccins = $carnetRepo->findBy(['patient' => $patient]);
        $listVaccins = $carnetRepo->findListVaccinsInCarnet($patient);

        return $this->render("praticien/carnet.html.twig",[
          'listVaccins' => $listVaccins,
        ]);
    }

    /**
     * @Route("/see_intervention/{patient_id}", name="see_intervention")
     */
    public function see_intervention(Request $request, $patient_id){
        $patient =$this->carnetVaccinationRepository->find($patient_id);
        $list = $this->interventionVaccinationRepository->searchInt($patient);

        return $this->render("praticien/inter.html.twig",[
            'intervention' => $list,
        ]);
    }


    /**
     * @Route("/update/edit", name="change_status")
     * @throws Exception
     */
      public function  update( Request $request, TranslatorInterface $translator, VaccinGenerate $vaccGen)
      {
            $id= $request->request->get('id');
            $action = $request->request->get('action');
            $type = $request->request->get('type');
            $praticien = $request->request->get('praticien');
            $praticien= $this->praticienRepository->find($praticien);
            $carnet = $request->request->get('carnet');
            $numero = $request->request->get('numero');


            switch ($action){
                case 'active':
                    if($type == "consultation"){
                        $ordoConsu = $this->ordoConsultationRepository->find($id);
                        if($ordoConsu != null){
                            $ordoConsu->setStatusConsultation(1);
                            $this->entityManager->persist($ordoConsu);
                            $this->entityManager->flush();
                        }
                    }else{
                        switch ($type){
                            case "test":
                                $ordoVacc = $this->interventionVaccinationRepository->find($id);
                                if($ordoVacc != null){
                                   $ordoVacc->setEtat(1);
                                    $this->entityManager->persist($ordoVacc);
                                    $this->entityManager->flush();
                                    $carnetvaccination = $this->carnetVaccinationRepository->find($carnet);
                                    $carnetvaccination->setEtat("1");
                                    $carnetvaccination->setPraticien($praticien);
                                    $carnetvaccination->setLot("lot");
                                    $this->entityManager->persist($carnetvaccination);
                                    $this->entityManager->flush();

                                }

                             break;

                            case "vaccination":
                                $pat = $request->request->get('patient');
                                $patient =  $this->patientRepository->find($pat);
                                $Date_Rdv= new DateTime('now');
                                $ordoVacc = $this->ordoVaccinationRepository->find($id);
                                if($ordoVacc != null){
                                    $this->vaccinGenerate->generateCalendar($patient, $Date_Rdv);
                                    $ordoVacc->setStatusVaccin(1);
                                    $this->entityManager->persist($ordoVacc);
                                    $this->entityManager->flush();

                                }
                                break;
                            case "intervention":
                                $ordoVacc = $this->interventionVaccinationRepository->find($id);
                                if($ordoVacc != null){
                                    $ordoVacc->setStatusVaccin(1);
                                    $this->entityManager->persist($ordoVacc);
                                    $this->entityManager->flush();
                                    $carnetvaccination = $this->carnetVaccinationRepository->find($carnet);
                                    $carnetvaccination->setStatus(1);
                                    $this->entityManager->persist($carnetvaccination);
                                    $this->entityManager->flush();

                                }

                                break;
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
                        switch ($type){
                            case 'vaccination':
                                $ordoVacc = $this->ordoVaccinationRepository->find($id);
                                if($ordoVacc != null){
                                    $ordoVacc->setStatusVaccin(2);
                                    $ordoVacc->setStatusNotif(1);
                                    $this->entityManager->persist($ordoVacc);
                                    $this->entityManager->flush();
                                }
                            break;

                            case 'intervention':
                                $intervention = $this->interventionVaccinationRepository->find($id);
                                if($intervention != null){
                                    $intervention->setStatusVaccin(2);
                                    $this->entityManager->persist($intervention);
                                    $this->entityManager->flush();
                                }
                            break;
                            case "proposition":
                                $proposition = $this->propositionRdvRepository->find($id);
                                if($proposition !=null){
                                    $proposition->setEtat(2);
                                    $this->entityManager->persist($proposition);
                                    $this->entityManager->flush();
                                }
                            break;
                        }

                    }
                    $message=$translator->trans('Successful change');
                    $this->addFlash('success', $message);
                    return new JsonResponse(['status' => 'OK']);
                    break;
            }
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
     * @Route("/organize_vaccination", name="organize_vaccination")
     */
    public function organize_vaccination(Request $request)
        {
            $inter = [];
            $inter['id'] = $request->request->get('id');
            $intervention = $this->interventionVaccinationRepository->find($inter['id']);

            $carnet['id']= $intervention->getId();
            $carnet['patient']= $intervention->getPatient()->getId();
            $carnet['vaccin']= $intervention->getVaccin()->getId();
            $carnet['carnet']= $intervention->getCarnet()->getId();
            $carnet['date']= $intervention->getDatePriseVaccin();
            $praticien= $request->request->get('praticien');
            $date = $carnet['date']->format('d-m-Y H:i:s');
            $carnet['date'] = str_replace("-", "/", explode(' ', $date)[0]);
            $carnet['heure'] = explode(' ', $date)[1];
            $form = $this->createForm(CarnetType::class, $carnet);
            $response = $this->renderView('praticien/_form_interven.html.twig', [
                'new' => false,
                'form' => $form->createView(),
                'praticien'=>$praticien,
                'eventData' => $carnet,
            ]);
            $form->handleRequest($request);
            return new JsonResponse(['form_html' => $response]);
        }

    /**
     * @Route("/add_vaccination_praticien",name="add_vaccination_praticien")
     */
    public function add_vaccination_praticien(Request $request)
    {
        $intervention = [];
        $intervention['id'] = $request->request->get('id');

        $carnetvaccina = $this->interventionVaccinationRepository->find($intervention['id'] );
        $carnet['id']= $carnetvaccina->getId();
        $carnet['patient']= $carnetvaccina->getPatient()->getId();
        $carnet['vaccin']= $carnetvaccina->getVaccin()->getId();
        $carnet['carnet']= $carnetvaccina->getCarnet()->getId();
        $carnet['date']= $carnetvaccina->getDatePriseVaccin();
        $date = $carnet['date']->format('d-m-Y H:i:s');
        $carnet['date'] = str_replace("-", "/", explode(' ', $date)[0]);
        $carnet['heure'] = explode(' ', $date)[1];
        $form = $this->createForm(CarnetType::class, $carnet);
        $response = $this->renderView('praticien/_form_intervention.html.twig', [
            'new' => false,
            'form' => $form->createView(),
            'eventData' => $carnet,
        ]);
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);

    }
    /**
     * @Route("/organize/calendrier", name = "organize_carnet")
     * @throws Exception
     */
    public function  organize_carnet(Request $request, TranslatorInterface $translator)
    {

        $intervention= $request->request->get('carnet');
        $praticien = $request->request->get('praticien');
        $praticien = $this->praticienRepository->find($praticien);
        $lot = $request->request->get('lot');
        $Id = $intervention['id'];
        $carnet = $intervention['carnet'];
        $inter = $this->interventionVaccinationRepository->find($Id);
        $inter->setEtat("1");
        $this->entityManager->persist($inter);
        $this->entityManager->flush();
        $carnetvaccination = $this->carnetVaccinationRepository->find($carnet);
        $carnetvaccination->setEtat("1");
        $carnetvaccination->setPraticien($praticien);
        $carnetvaccination->setLot($lot);
        $this->entityManager->persist($carnetvaccination);
        $this->entityManager->flush();

        $message=$translator->trans('realized');
        $this->addFlash('success', $message);
        return $this->redirectToRoute('vaccination_praticien');
    }

    /**
     * @Route("/realize/vaccination", name = "realize_vaccination")
     * @throws Exception
     */
    public function  realize_vaccination(Request $request, TranslatorInterface $translator)
    {

        $intervention= $request->request->get('carnet');
        $id=$intervention['id'];
        $date=$intervention['date'];
        $heure=$intervention['heure'];
        $carnet = $intervention['carnet'];
        $rdv_date = str_replace("/", "-", $date);
        $Date_Rdv = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$heure)));
        $interve = $this->interventionVaccinationRepository->find($id);
        $interve->setDatePriseVaccin($Date_Rdv);
        $interve->setStatusVaccin("1");
        $this->entityManager->persist($interve);
        $this->entityManager->flush();
        $carnetvaccination = $this->carnetVaccinationRepository->find($carnet);
        $carnetvaccination->setStatus(1);
        $carnetvaccination->setDatePrise($Date_Rdv);
        $this->entityManager->persist($carnetvaccination);
        $this->entityManager->flush();
        $message=$translator->trans('Successful change');
        $this->addFlash('success', $message);
        return $this->redirectToRoute('vaccination_praticien');
    }



    /**
     * @Route("/add_rdv_praticien", name="add_rdv_praticien")
     */
    public function add_rdv_praticien(Request $request)
    {
        $rdv = [];
        $typeRdvArrays = [
            "consultation" => "CONSULTATION",
            "intervention" =>"INTERVENTION"
        ];
        $rdv['id'] = $request->request->get('id');

        $rdv['typeRdv'] = $request->request->get('type');
        if ($rdv['typeRdv'] == 'consultation'){
            $ordoCon = $this->ordoConsultationRepository->find($rdv['id']);
            $rdv['objet'] = $ordoCon->getObjetConsultation();
            $rdv['dateRdv'] = $ordoCon->getDateRdv();
        }elseif ($rdv['typeRdv'] == 'intervention'){
            $inter = $this->intervationConsultationRepository->find($rdv['id']);
            $rdv['objet']=$inter->getObjetConsultation();
            $rdv['dateRdv']=$inter->getDateConsultation();
        }
        if ($rdv['dateRdv'] != ''){
            $date = $rdv['dateRdv']->format('d-m-Y H:i:s');
            $rdv['dateRdv'] = str_replace("-", "/", explode(' ', $date)[0]);
            $rdv['heureRdv'] = explode(' ', $date)[1];
        }

        $form = $this->createForm(RdvType::class, $rdv, ['typeRdvArrays' => $typeRdvArrays]);
        $response = $this->renderView('praticien/_form_accept.html.twig', [
            'new' => false,
            'form' => $form->createView(),
            'eventData' => $rdv,
        ]);
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);

    }
    /**
     * @Route("/realize/rdv", name = "realize_rdv")
     */
    public function  realize_rdv(Request $request, TranslatorInterface $translator)
    {
        $Id = $request->request->get('id');
        $type= $request->request->get('type');
        switch ($type){
            case 'RealizeCons':
                if ($Id != ''){
                    $ordoconsultation = $this->ordoConsultationRepository->find($Id);
                    $ordoconsultation->setEtat("1");
                    $this->entityManager->persist($ordoconsultation);
                    $this->entityManager->flush();
                }
                break;

            case 'RealizeInt':
                if ($Id != ''){
                    $inter = $this->intervationConsultationRepository->find($Id);
                    $inter->setEtat("1");
                    $this->entityManager->persist($inter);
                    $this->entityManager->flush();
                }
                break;
            case 'AnnulCons':
                if ($Id != ''){
                    $ordoconsultation = $this->ordoConsultationRepository->find($Id);
                    $ordoconsultation->setStatusConsultation("2");
                    $this->entityManager->persist($ordoconsultation);
                    $this->entityManager->flush();
                }
                break;

            case 'AnnulInt':
                if ($Id != ''){
                    $inter = $this->intervationConsultationRepository->find($Id);
                    $inter->setStatus("2");
                    $this->entityManager->persist($inter);
                    $this->entityManager->flush();
                }
                break;

        }
        $message=$translator->trans('Successful change');
        $this->addFlash('success', $message);
        return new JsonResponse(['status' => 'OK']);
    }

    /**
     * @Route("/accept_rdv" ,name="accept_rdv")
     * @throws Exception
     */
    public function accept_rdv(Request $request, TranslatorInterface $translator)
    {
        $rdvRequest = $request->request->get("rdv");
        $Id= $rdvRequest['id'];
        $date = $rdvRequest['dateRdv'];
        $heure = $rdvRequest["heureRdv"];
        $rdv_date = str_replace("/", "-", $date);
        $Date_Rdv = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$heure)));
        $type = $rdvRequest['typeRdv'];
        switch ($type){
            case 'consultation':
                if ($Id != ''){
                    $ordoconsultation = $this->ordoConsultationRepository->find($Id);
                    $ordoconsultation->setStatusConsultation("1");
                    $ordoconsultation->setDateRdv($Date_Rdv);
                    $this->entityManager->persist($ordoconsultation);
                    $this->entityManager->flush();
                }
                break;

            case 'intervention':
                if ($Id != ''){
                    $inter = $this->intervationConsultationRepository->find($Id);
                    $inter->setStatus("1");
                    $inter->setDateConsultation($Date_Rdv);
                    $this->entityManager->persist($inter);
                    $this->entityManager->flush();
                }
                break;


        }
        $message=$translator->trans('Appointment registration successful');
        $this->addFlash('success', $message);
        return $this->redirectToRoute('rdv_praticien');
    }

    /**
     *  @Route("/rdv/praticien/associer", name="rdv_associer")
     *
     */
    public function rdv_associer(){
        $assoce= [];
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $associer = $this->associerRepository->searchAssocier($praticien);
        foreach ($associer as $value) {
            $lastname = $value["lastName"];
            $firstname = $value["firstName"];
            $pat = $value["patient"];
        }
        $patient = [
            $pat => $lastname . '  ' . $firstname
        ];
        $typeRdvArrays = [
            "consultation" => "CONSULTATION",
            "intervention" =>"INTERVENTION"
        ];
        $form = $this->createForm(RdvAssocieType::class,$assoce, ['patient' => $patient, 'typeRdvArrays' => $typeRdvArrays]);
        return $this->render('praticien/_form_rdv_associer.html.twig',[
            'new'=> true,
            'form'=>$form->createView(),
            'eventData'=> $assoce,
        ]);
    }





    /**
     * @Route("/form-add-proposition", name="add_form_proposition_rdv")
     * @param Request $request
     * @return JsonResponse
     */
    public function add_form_proposition (Request $request)
    {
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $associer = $this->associerRepository->searchAssocier($praticien);

            foreach ($associer as $value) {
                    $lastname = $value["lastName"];
                    $firstname = $value["firstName"];
                    $pat = $value["patient"];
            }

            $patient = [
                $pat => $lastname . '  ' . $firstname
                ];

            $typeRdvArrays = [
                "consultation" => "Consultation",
                "vaccination" => "Vaccin"
            ];
            $action = $request->request->get('action');
            $rdv = [];
            if ($action == "new") {
                $form = $this->createForm(PropositionRdvType::class, $rdv, ['patient' => $patient, 'typeRdvArrays' => $typeRdvArrays]);
                $response = $this->renderView('praticien/_form_proposition.html.twig', [
                    'new' => true,
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
        $vaccin= $this->propositionRdvRepository->searchStatusPraticienv($praticien);
        $associer = $this->associerRepository->searchAssocier($praticien);

        return $this->render('praticien/proposition.html.twig',[
            'consultation'=>$prop,
            'vaccination'=>$vaccin,
            'associer'=>$associer,
        ]);
    }

    /**
     * @Route("/register_proposition", name ="register_proposition")
     * @throws Exception
     */

    public  function register_proposition(Request $request)
    {
        $propositionRequest=$request->request->get("proposition_rdv");
        $description = $propositionRequest["description"];
        $patient = $propositionRequest["patient"];
        $date =$propositionRequest["dateRdv"];
        $heure = $propositionRequest["heureRdv"];
        $vaccine = $propositionRequest["vaccin"];
        $vaccine = $this->vaccinRepository->find($vaccine);
        $Id = $propositionRequest["id"];
        $type = $propositionRequest["typeRdv"];
        $user = $this->getUser();
        $rdv_date = str_replace("/", "-", $date);
        $Date_Rdv = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$heure)));
        $praticien= $this->praticienRepository->findOneBy(['user'=>$user]);
        if($patient != ''){
            $patient =  $this->patientRepository->find($patient);
        }

        switch ($type){
            case 'consultation':
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

                $proposition->setType("consultation");
                $this->entityManager->persist($proposition);
                $this->entityManager->flush();
                return $this->redirectToRoute('rdv_proposition');
            break;
            case 'vaccination':
                if($Id !='')
                {
                    $proposition = $this->propositionRdvRepository->find($Id);
                }else {
                    $proposition= new PropositionRdv();
                }
                $proposition->setVaccin($vaccine);
                $proposition->setDateProposition($Date_Rdv);
                $proposition->setPraticien($praticien);
                $proposition->setPatient($patient);
                $proposition->setStatusProposition(0);
                $proposition->setEtat(0);

                $proposition->setType("vaccination");
                $this->entityManager->persist($proposition);
                $this->entityManager->flush();
                return $this->redirectToRoute('rdv_proposition');
            break;
        }

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
        $patientCons = $this->ordoConsultationRepository->searchConsultation($praticien);
        $patient = $this->associerRepository->searchPatient($praticien);
        $nb= $this->ordoVaccinationRepository->countUnrealizedVacc($praticien);
        $realize = $this->ordoVaccinationRepository->countrealizedVacc($praticien);

        $nbUnrealizedVacc = $this->interventionVaccinationRepository->countUnrealizedVacc($praticien);

        $nbRealizedVacc = $this->interventionVaccinationRepository->countRealizedVacc($praticien);
         if($patient != null) {
             foreach ($patient as $pat) {
                 $patient = $pat[1];
             }
         }
          foreach ($nb as $n){
              $unreal = $n[1];
              foreach ($nbUnrealizedVacc as $nb){
                    $unre = $nb[1];
                    $nbUnrealizedVacc =$unreal+$unre;
              }
          }
          foreach ($realize as $real){
              $rea = $real[1];
              foreach ($nbRealizedVacc as $nbr){
                $nbRealized = $nbr[1];
                $nbRealizedVacc = $rea + $nbRealized;
              }
          }

        return $this->render('praticien/dashboard.html.twig', [
            "nbPatient"=>$patient,
            "nbUnrealizedVacc" => $nbUnrealizedVacc,
            "nbRealizedVacc" => $nbRealizedVacc,
            "nbConsultation" => $patientCons[0][1]
        ]);
    }

    /**
    * @Route("/chart/nb_prise_type_vacc", name="chart/nb_prise_type_vacc"), methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
    */
    public function nb_prise_type_vacc(){
      $user=$this->getUser();
      $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
      $queryResult = $this->vaccinRepository->countPriseVaccinParType($praticien);
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
     * @throws Exception
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

    /**
     *  @Route("/generate/vaccin", name="vaccin_generate")
     *
     */
    public function generate_vaccin(){
        $generationvacc= [];
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $associer = $this->associerRepository->searchAssocier($praticien);
        foreach ($associer as $value) {
            $lastname = $value["lastName"];
            $firstname = $value["firstName"];
            $pat = $value["patient"];
        }
        $patient = [
            $pat => $lastname . '  ' . $firstname
        ];
        $form = $this->createForm(GenerationVaccinType::class,$generationvacc, ['patient' => $patient]);
        return $this->render('praticien/_form_generate_vaccin.html.twig',[
            'new'=> true,
            'form'=>$form->createView(),
            'eventData'=> $generationvacc,
        ]);
    }

    /**
     * @Route("/vaccin/generate", name="generate_vaccin")
     * @param Request $request
     * @param $translator
     * @return Response
     * @throws Exception
     */
    public function  vaccin_generate(Request $request, TranslatorInterface $translator){
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $request = $request->request->get('generation_vaccin');
        $patient = $request['patient'];
        $patient= $this->patientRepository->find($patient);
        $vaccin = $request['vaccin'];
        $vaccin = $this->vaccinRepository->find($vaccin);
        $identification = $request['identification'];
        $date = $request['date_prise'];
        $heure = $request['heureprise'];
        $rdv_date = str_replace("/", "-", $date);
        $Date_Rdv = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$heure)));
        $carnet = new CarnetVaccination();
        $carnet->setStatus("1");
        $carnet->setDatePrise($Date_Rdv);
        $carnet->setVaccin($vaccin);
        $carnet->setIdentification($identification);
        $carnet->setPatient($patient);
        $this->entityManager->persist($carnet);
        $this->entityManager->flush();
        $intervention = new InterventionVaccination();
        $intervention->setCarnet($carnet);
        $intervention->setStatusVaccin("1");
        $ordonance = $this->ordonnaceRepository->findOneBy(['praticien'=>$praticien]);
        $intervention->setOrdonnace($ordonance);
        $intervention->setPatient($patient);
        $intervention->setVaccin($vaccin);
        $intervention->setDatePriseVaccin($Date_Rdv);
        $intervention->setEtat("0");
        $this->entityManager->persist($intervention);
        $this->entityManager->flush();
        $message=$translator->trans('registration successful');
        $this->addFlash('success', $message);
        return $this->redirectToRoute('vaccination_praticien');
    }
    /**
    * @Route("/register_rdv_associer", name="register_rdv_associer")
    */
    public function register_rdv_associer(Request $request,TranslatorInterface $translator){
        $associer = $request->request->get('rdv_associe');
        $user= $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $ordo = $this->ordonnaceRepository->findOneBy(['praticien' => $praticien]);
        $patient = $associer['patient'];
        $patient = $this->patientRepository->find($patient);
        $type = $associer['typeRdv'];
        $Id = $associer['id'];
        $objet = $associer['objet'];
        $date = $associer['dateRdv'];
        $heure = $associer['heureRdv'];
        $rdv_date = str_replace("/", "-", $date);
        $Date_Rdv = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$heure)));
        switch ($type){
            case 'consultation':
                if ($Id != ''){
                    $ordoconsultation = $this->ordoConsultationRepository->find($Id);
                }else{
                    $ordoconsultation = new OrdoConsultation();
                }

                $ordoconsultation->setObjetConsultation($objet);
                $ordoconsultation->setStatusConsultation(1);
                $ordoconsultation->setEtat(0);
                $ordoconsultation->setPatient($patient);
                $ordoconsultation->setDateRdv($Date_Rdv);
                $ordoconsultation->setOrdonnance($ordo);
                $this->entityManager->persist($ordoconsultation);
                $this->entityManager->flush();
                break;

            case 'intervention':
                if ($Id != ''){
                    $inter = $this->intervationConsultationRepository->find($Id);
                }else{
                    $inter = new IntervationConsultation();
                }
                $inter->setPatient($patient);
                $inter->setStatus(1);
                $inter->setEtat(0);
                $inter->setObjetConsultation($objet);
                $inter->setDateConsultation($Date_Rdv);
                $inter->setOrdonnace($ordo);
                $this->entityManager->persist($inter);
                $this->entityManager->flush();
        }
        $message=$translator->trans('Appointment registration successful');
        $this->addFlash('success', $message);
        return $this->redirectToRoute('rdv_praticien');

    }


    /**
     * @Route("/create-rdv/praticien", name="create_rdv_praticien")
     * @param Request $request
     * @return Response
     */
    public function create_rdv_praticien(Request $request)
    {
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $associer = $this->associerRepository->searchAssocier($praticien);

        foreach ($associer as $value) {
            $lastname = $value["lastName"];
            $firstname = $value["firstName"];
            $pat = $value["patient"];
        }

        $patient = [
            $pat => $lastname . '  ' . $firstname
        ];

        $typeRdvArrays = [
            "consultation" => "Consultation",
            "vaccination" => "Vaccin"
        ];
        $action = $request->request->get('action');
        $rdv = [];

        $form = $this->createForm(PropositionRdvType::class, $rdv, ['patient' => $patient, 'typeRdvArrays' => $typeRdvArrays]);
        return $this->render('praticien/_form_proposition.html.twig', [
            'new' => true,
            'form' => $form->createView(),
            'eventData' => $rdv,
        ]);
    }

    /**
     * @Route("/vaccin", name="vaccin")
     */
    public function vaccin(){
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $associer = $this->associerRepository->searchAssocier($praticien);
        $data = 0;
        if ($associer != null)$data = 1;


        return new JsonResponse($data);
    }


}
