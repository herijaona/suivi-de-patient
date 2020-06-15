<?php

namespace App\Controller\Praticien;

use App\Repository\FamilyRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\RendezVousRepository;
use App\Service\VaccinGenerate;
use Doctrine\ORM\EntityManagerInterface;
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
    protected $rendezVousRepository;
    protected $entityManager;

    function __construct(
        VaccinGenerate $vaccinGenerate,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        FamilyRepository $familyRepository,
        EntityManagerInterface $entityManager,
        RendezVousRepository $rendezVousRepository
    )
    {
        $this->vaccinGenerate = $vaccinGenerate;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->familyRepository = $familyRepository;
        $this->rendezVousRepository = $rendezVousRepository;
        $this->entityManager = $entityManager;
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
        $all_rdv = $praticien->getRendezVous();
        $event = [];
        foreach ($all_rdv as $rdv){
            $element = [
                'title'=>($rdv->getVaccin() != null && $rdv->getVaccin()->getVaccinName() != null) ? $rdv->getVaccin()->getVaccinName() : ($rdv->getType() == 2 ? "Demander de consultation" : ($rdv->getType() == 3 ? 'Demander de Rendez-vous': '')),
                'start'=>$rdv->getDateRdv()->format('Y-m-d'),
                'id'=>$rdv->getId(),
                'color'=> $rdv->getEtat() == 0 ? '#1e7e34':'#1fdc25'
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
        $patient = $this->praticienRepository->findOneBy(['user'=>$user]);
        $all_rdv = $patient->getRendezVous();
        $data =[];
        $doctor = $this->praticienRepository->findAll();
        foreach ($all_rdv as $rdv){
            if($rdv->getType() == 2){
                $rdv->detail = $rdv->getDescription();
                //$rdv->dateRdv = Carbon::parse($rdv->getDateRdv())->locale('fr_FR')->isoFormat('dddd d MMMM Y');
                if($rdv->getPatient()){
                    $rdv->nom = $rdv->getPatient()->getFirstName()." ".$rdv->getPatient()->getLastName();
                    $rdv->phone = $rdv->getPatient()->getPhone();
                }else{
                    $rdv->nom  = "";
                    $rdv->phone = "";
                }
                array_push($data,$rdv);
            }
        }

        return $this->render('praticien/consultation.html.twig', [
            'Consultations' => $data,
            'Doctors' => $doctor,
            'type' => 2
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
        $patient = $this->praticienRepository->findOneBy(['user'=>$user]);
        $all_rdv = $patient->getRendezVous();
        $data =[];
        foreach ($all_rdv as $rdv){
            if($rdv->getType() == 1){
                $rdv->detail = $rdv->getVaccin()->getVaccinName();
                //$rdv->dateRdv = Carbon::parse($rdv->getDateRdv())->locale('fr_FR')->isoFormat('dddd d MMMM Y');
                if($rdv->getPatient()){
                    $rdv->nom = $rdv->getPatient()->getFirstName()." ".$rdv->getPatient()->getLastName();
                    $rdv->phone = $rdv->getPatient()->getPhone();
                }else{
                    $rdv->nom  = "";
                    $rdv->phone = "";
                }
                array_push($data,$rdv);
            }
        }
        return $this->render('praticien/vaccination.html.twig', [
            'Vaccinations'=>$data,
            'type' => 1
        ]);
    }



    /**
     * @Route("/rdv", name="rdv_praticien")
     */
    public function rdv_patient()
    {
        if (!$this->isGranted('ROLE_PRATICIEN')) {
            return $this->redirectToRoute('homepage');
        }
        $user = $this->getUser();
        $patient = $this->praticienRepository->findOneBy(['user'=>$user]);
        $all_rdv = $patient->getRendezVous();
        $data =[];
        $doctor = $this->praticienRepository->findAll();

        foreach ($all_rdv as $rdv){
            if($rdv->getType() == 3){
                $rdv->detail = $rdv->getDescription();
                //$rdv->dateRdv = Carbon::parse($rdv->getDateRdv())->format('d/m/y')->locale('fr_FR')->isoFormat('dddd d MMMM Y');
                if($rdv->getPatient()){
                    $rdv->nom = $rdv->getPatient()->getFirstName()." ".$rdv->getPatient()->getLastName();
                    $rdv->phone = $rdv->getPatient()->getPhone();
                }else{
                    $rdv->nom  = "";
                    $rdv->phone = "";
                }
                array_push($data,$rdv);
            }
        }

        return $this->render('praticien/rdv.html.twig', [
            'Consultations' => $data,
            'Doctors' => $doctor,
            'type' => 3
        ]);
    }

    /**
     * @Route("/update-rdv", name="update_etat_rdv")
     */
    public function update_etat_rdv(Request $request)
    {
        $Rdv = $this->rendezVousRepository->find($request->request->get("id_rdv"));
        if ($request->request->get("etat") == 0){
            $Rdv->setEtat(true);
        }elseif ($request->request->get("etat") == 1){
            $Rdv->setEtat(false);
        }

        $this->entityManager->persist($Rdv);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 'OK']);
    }

    /**
     * @Route("/update-rdv-status", name="update_status_rdv")
     */
    public function update_status_rdv(Request $request)
    {
        $Rdv = $this->rendezVousRepository->find($request->request->get("id_rdv"));
        //dd($Rdv, $request->request->get("id_rdv"), $request->request->get("status"));
            if ($request->request->get("status") == 0){
                $Rdv->setStatus(1);
            }elseif ($request->request->get("status") == 1){
                $Rdv->setStatus(0);
            }
        $this->entityManager->persist($Rdv);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 'OK']);
    }

}
