<?php

namespace App\Controller\Praticien;

use App\Entity\IntervationConsultation;
use App\Entity\InterventionVaccination;
use App\Entity\OrdoConsultation;
use App\Entity\OrdoVaccination;
use App\Form\ConsultationPraticienType;
use App\Repository\FamilyRepository;
use App\Repository\IntervationConsultationRepository;
use App\Repository\InterventionVaccinationRepository;
use App\Repository\OrdoConsultationRepository;
use App\Repository\OrdoVaccinationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\VaccinRepository;
use App\Service\VaccinGenerate;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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


    function __construct(
        VaccinGenerate $vaccinGenerate,
        PatientRepository $patientRepository,
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
        $this->entityManager = $entityManager;
        $this->ordoConsultationRepository = $ordoConsultationRepository;
        $this->ordoVaccinationRepository = $ordoVaccinationRepository;
        $this->intervationConsultationRepository=$intervationConsultationRepository;
        $this->interventionVaccinationRepository=$interventionVaccinationRepository;
        $this->vaccinRepository = $vaccinRepository;
    }
    /**
     * @Route("/agenda", name="praticien")
     */
    public function praticien()
    {
        if (!$this->isGranted('ROLE_PRATICIEN')) {
            return $this->redirectToRoute('homepage');
        }
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
       // $all_rdv = $this->rendezVousRepository->findCalendarPraticien($praticien->getId());
        $all_rdv = [];
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
                'id' => $rdv->getId(),
                'title' => ($rdv->getVaccin() != null && $rdv->getVaccin()->getVaccinName() != null) ? $rdv->getVaccin()->getVaccinName() : $rdv->getDescription() .' - '. (($rdv->getPatient() != null && $rdv->getPatient()->getFirstName() != null) ? $rdv->getPatient()->getFirstName() : 'Proposition Consultation'),
                'start' => $rdv->getDateRdv()->format('Y-m-d'),
                'id' => $rdv->getId(),
                'color' => $color
            ];
            array_push($event,$element);
        }
        return $this->render('praticien/agenda.html.twig', [
            'Events'=>$event
        ]);
    }

    /**
     * @Route("/consultation", name="consultaion_praticien")
     */
    public function consultaion_patient()
    {
        if (!$this->isGranted('ROLE_PRATICIEN')) {
            return $this->redirectToRoute('homepage');
        }
        $user = $this->getUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $rvc = $this->ordoConsultationRepository->searchStatusPraticien($praticien->getId(), 1,0);

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
        $rvc = $this->ordoVaccinationRepository->searchStatusPraticien($praticien->getId(), 1, 0 );

        return $this->render('praticien/vaccination.html.twig', [
            'vaccination' => $rvc,
        ]);
    }
    /**
     * @Route("/intervention/actived", name="intervention_praticien")
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
        $icp= $this->intervationConsultationRepository->searchIntervationPraticien($praticien->getId(),0);
        $ivp= $this->interventionVaccinationRepository->searchIntervationPraticien($praticien->getId(),0);
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
        $rce = $this->ordoConsultationRepository->searchStatusPraticien($praticien->getId(), 2);
        $rve = $this->ordoVaccinationRepository->searchStatusPraticien($praticien->getId(), 2);
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
        $rve = $this->ordoVaccinationRepository->searchStatusPraticienEnValid($praticien->getId());
        $vaccin= $this->vaccinRepository->findAll();
        return $this->render('praticien/rdv.html.twig', [
            'consultation'=>$rce,
            'vaccination'=>$rve,
            'Vaccin'=>$vaccin
        ]);
    }

    /**
     * @Route("/update/edit", name="change_status")
     */
      public function  update( Request $request)
      {
          if($request->request->get('action') == "active")
          {
              if($request->request->get('type') == "vaccination" && $request->request->get('status') == 0){
                  $ordoVacc = $this->ordoVaccinationRepository->find($request->request->get('id'));
                  if($ordoVacc != null){
                      $ordoVacc->setStatusVaccin(1);
                      $this->entityManager->persist($ordoVacc);
                      $this->entityManager->flush();
                  }
              }else{
                  $ordoConsu = $this->ordoConsultationRepository->find($request->request->get('id'));
                  if($ordoConsu != null){
                      $ordoConsu->setstatusConsultation(1);
                      $this->entityManager->persist($ordoConsu);
                      $this->entityManager->flush();
                  }
              }
              $this->addFlash('success', 'Changement effectué avec succès');
              return new JsonResponse(['status' => 'OK']);
          }
          elseif ($request->request->get('action') == "reject"){
                  if($request->request->get('type') == "vaccination" && $request->request->get('status')== 0){
                      $ordoVacc = $this->ordoVaccinationRepository->find($request->request->get('id'));
                      if($ordoVacc != null){
                          $ordoVacc->setStatusVaccin(2);
                          $this->entityManager->persist($ordoVacc);
                          $this->entityManager->flush();
                      }
                  }else{
                      $ordoConsu = $this->ordoConsultationRepository->find($request->request->get('id'));
                      if($ordoConsu != null){
                          $ordoConsu->setstatusConsultation(2);
                          $this->entityManager->persist($ordoConsu);
                          $this->entityManager->flush();
                      }
                  }
                  $this->addFlash('success', 'Changement effectué avec succès');
                  return new JsonResponse(['status' => 'OK']);
          }
      }

    /**
     * @Route("/update/etat", name="update_etat")
     */
       public function update_etat(Request $request)
           {
               $id= $request->request->get('id');
               $praticien = $request->request->get('praticien');
               $patient = $request->request->get('patient');
               $date = $request->request->get('date');
               $vaccin = $request->request->get('vaccin');
               $vaccination= $this->vaccinRepository->find($vaccin);
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
                           $interConsu->setEtat(1);
                           $this->entityManager->persist($interConsu);
                           $this->entityManager->flush();
                           $ordoConsu->setEtat(1);
                           $this->entityManager->persist($ordoConsu);
                           $this->entityManager->flush();
                       }
                   }elseif($request->request->get('type') == "vaccination" && $request->request->get('etat') == 0){
                       $ordoVacc = $this->ordoVaccinationRepository->find($request->request->get('id'));
                       if($ordoVacc != null){
                           $interVacc = new  InterventionVaccination();
                           $interVacc->setPatient($patient);
                           $interVacc->setPraticienPrescripteur($praticien);
                           $interVacc->setEtat(1);
                           $interVacc->setVaccin($vaccination);
                           $interVacc->setDatePriseVaccin( $Date_Rdv);
                           $interVacc->setPraticienExecutant($praticien);
                           $interVacc->setOrdoVaccination($ordovacc);
                           $this->entityManager->persist($interVacc);
                           $this->entityManager->flush();
                           $ordoVacc->setEtat(1);
                           $this->entityManager->persist($ordoVacc);
                           $this->entityManager->flush();
                       }
                   }
                   $this->addFlash('success', 'Changement effectué avec succès');
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
                           $ordoConsu->setEtat(1);
                           $this->entityManager->persist($ordoConsu);
                           $this->entityManager->flush();
                       }
                   }
                   elseif($request->request->get('type') == "vaccination" && $request->request->get('etat') == 0){
                       $ordoVacc = $this->ordoVaccinationRepository->find($request->request->get('id'));
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
                           $ordoVacc->setEtat(1);
                           $this->entityManager->persist($ordoVacc);
                           $this->entityManager->flush();
                       }
                   }
               }
               $this->addFlash('success', 'Changement effectué avec succès');
               return new JsonResponse(['status' => 'OK']);


           }
    /**
     * @Route("/update-rdv", name="update_etat_rdv")
     */
    public function update_etat_rdv(Request $request)
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
        $this->addFlash('success', 'Changement effectué avec succès');
        return new JsonResponse(['status' => 'OK']);
    }

    /**
     * @Route("/update-rdv-status", name="update_status_rdv")
     */
    public function update_status_rdv(Request $request)
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
            $this->addFlash('success', 'Changement effectué avec succès');
        }else{
            $this->addFlash('error', 'Vous n\'êtes pas d\'accès à cette évenement ');
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
        $rdv->setDateRdv($Date_Rdv);
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

}
