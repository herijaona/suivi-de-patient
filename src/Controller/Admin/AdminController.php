<?php

namespace App\Controller\Admin;

use App\Entity\Vaccin;
use App\Form\CenterHealthType;
use App\Form\VaccinType;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\TypeVaccinRepository;
use App\Repository\VaccinRepository;
use App\Service\VaccinGenerate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    protected $vaccinGenerate;
    protected $patientRepository;
    protected $praticienRepository;
    protected $vaccinRepository;
    protected $typeVaccinRepository;
    protected $entityManager;

    function __construct(
        VaccinGenerate $vaccinGenerate,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        VaccinRepository $vaccinRepository,
        TypeVaccinRepository $typeVaccinRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->vaccinGenerate = $vaccinGenerate;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->vaccinRepository = $vaccinRepository;
        $this->typeVaccinRepository = $typeVaccinRepository;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/", name="admin")
     */
    public function index()
    {
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);

    }

    /**
     * @Route("/vaccin", name="vaccin_admin")
     */
    public function vaccin_admin()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('homepage');
        }
        //$all_rdv = $this->rendezVousRepository->findRdvByAdmin(1);
        $all_rdv = [];
        return $this->render('admin/vaccin.html.twig', [
            'Vaccinations' => $all_rdv,
            'type' => 1
        ]);
    }

    /**
     * @Route("/praticien", name="praticiens_admin")
     */
    public function praticiens_admin()
    {

        $praticien = $this->praticienRepository->findByPraticien();

        return $this->render('admin/praticien.html.twig', [
            'praticiens' => $praticien,
        ]);

    }

    /**
     * @Route("/patient", name="patients_admin")
     */
    public function patients_admin()
    {
        $patient = $this->patientRepository->findByPatient();
        return $this->render('admin/patient.html.twig', [
            'patients' => $patient,
        ]);

    }

    /**
     * @Route("/membres-admin", name="admin_member")
     */
    public function admin_member()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('homepage');
        }

        $praticiens = $this->praticienRepository->findBy([], ['firstName' => 'ASC']);
        $patients = $this->patientRepository->findBy([], ['firstName' => 'ASC']);

        return $this->render('admin/membres.html.twig', [
            'praticiens' => $praticiens,
            'patients' => $patients,
        ]);
    }

    /**
     * @Route("/vaccin-admin", name="admin_vaccin")
     */
    public function admin_vaccin_()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('homepage');
        }

        $vaccin = $this->vaccinRepository->findBy([],['vaccinName' => 'ASC']);
        return $this->render('admin/vaccin/index.html.twig', [
            'vaccins' => $vaccin,
        ]);
    }

    /**
     * @Route("/form-add-vaccin", name="add_form_vaccin", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @return JsonResponse
     */
    public function add_form_vaccin(Request $request)
    {
        $action = $request->request->get('action');
        $idVaccin = $request->request->get('id_vaccin');
        $eventData = [];
        $set = false;
        if ($action == "new") {
            $vaccin = new Vaccin();
            $form = $this->createForm(VaccinType::class, $vaccin);
            $response = $this->renderView('admin/vaccin/new_form_vaccin.html.twig', [
                'new' => true,
                'form' => $form->createView(),
                'eventData' => $eventData,
                'set' => $set
            ]);
        } else {
            $vaccin = $this->vaccinRepository->find($idVaccin);

            $form = $this->createForm(VaccinType::class, $vaccin);
            $response = $this->renderView('admin/vaccin/new_form_vaccin.html.twig', [
                'new' => false,
                'form' => $form->createView(),
                'eventData' => $eventData,
                'set' => $set
            ]);
        }
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);
    }

    /**
     * @Route("/vaccin/register", name="register_vaccin", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function register_vaccin(Request $request)
    {
        $vaccinRequest = $request->request->get('vaccin');

        $VaccinName = $vaccinRequest['vaccinName'];
        $TypeVaccin = $vaccinRequest['TypeVaccin'];
        $vaccinDescription = $vaccinRequest['vaccinDescription'];
        $datePriseInitiale = $vaccinRequest['datePriseInitiale'];
        $rappel1 = $vaccinRequest['rappel1'];
        $rappel2 = $vaccinRequest['rappel2'];
        $rappel3 = $vaccinRequest['rappel3'];
        $rappel4 = $vaccinRequest['rappel4'];
        $rappel5 = $vaccinRequest['rappel5'];
        $rappel6 = $vaccinRequest['rappel6'];
        $rappel7 = $vaccinRequest['rappel7'];
        $rappel8 = $vaccinRequest['rappel8'];
        $rappel9 = $vaccinRequest['rappel9'];
        $rappel10 = $vaccinRequest['rappel10'];
        $Status = false;
        if (isset($vaccinRequest['etat'])){
            $Status = true;
        }
        $TpVaccin = null;
        if ($TypeVaccin){
            $TpVaccin = $this->typeVaccinRepository->find($TypeVaccin);
        }

        $idVaccin = $vaccinRequest['id'];
        if($idVaccin != '' && $idVaccin != null){
            $Vaccin = $this->vaccinRepository->find($idVaccin);
            $Vaccin->setVaccinName($VaccinName);
            $Vaccin->setTypeVaccin($TpVaccin);
            $Vaccin->setVaccinDescription($vaccinDescription);
            $Vaccin->setDatePriseInitiale($datePriseInitiale);
            $Vaccin->setRappel1($rappel1);
            $Vaccin->setRappel2($rappel2);
            $Vaccin->setRappel3($rappel3);
            $Vaccin->setRappel4($rappel4);
            $Vaccin->setRappel5($rappel5);
            $Vaccin->setRappel6($rappel6);
            $Vaccin->setRappel7($rappel7);
            $Vaccin->setRappel8($rappel8);
            $Vaccin->setRappel9($rappel9);
            $Vaccin->setRappel10($rappel10);
            $Vaccin->setEtat($Status);
            $this->entityManager->persist($Vaccin);
            $this->entityManager->flush();
            $this->addFlash('success', 'modification avec succès !');
        }else{
            $VaccinNew = new Vaccin();
            $VaccinNew->setVaccinName($VaccinName);
            $VaccinNew->setTypeVaccin($TpVaccin);
            $VaccinNew->setVaccinDescription($vaccinDescription);
            $VaccinNew->setDatePriseInitiale($datePriseInitiale);
            $VaccinNew->setRappel1($rappel1);
            $VaccinNew->setRappel2($rappel2);
            $VaccinNew->setRappel3($rappel3);
            $VaccinNew->setRappel4($rappel4);
            $VaccinNew->setRappel5($rappel5);
            $VaccinNew->setRappel6($rappel6);
            $VaccinNew->setRappel7($rappel7);
            $VaccinNew->setRappel8($rappel8);
            $VaccinNew->setRappel9($rappel9);
            $VaccinNew->setRappel10($rappel10);
            $VaccinNew->setEtat($Status);
            $this->entityManager->persist($VaccinNew);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le nom de ville à été enregistré avec succès !');
        }
        return $this->redirectToRoute("admin_vaccin");
    }

    /**
     * @Route("/admin/vaccin/remove", name="remove_vaccin", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_vaccin(Request $request)
    {
        $idVaccin = $request->request->get('id_vaccin');
        $delete = false;
        if ($idVaccin != '' && $idVaccin != null){
            $Vaccin = $this->vaccinRepository->find($idVaccin);
            if (null !=  $Vaccin ){
                $VaccinCentreHealths = $Vaccin->getVaccinCentreHealths();
                $OrdoVaccinations = $Vaccin->getOrdoVaccinations();
                $InterventionVaccinations = $Vaccin->getInterventionVaccinations();
                $CarnetVaccinations = $Vaccin->getCarnetVaccinations();
                $VaccinPraticiens = $Vaccin->getVaccinPraticiens();
                $PatientVaccins = $Vaccin->getPatientVaccins();
                if (($VaccinCentreHealths && count($VaccinCentreHealths) > 0) ||
                    ($OrdoVaccinations && count($OrdoVaccinations) > 0) ||
                    ($InterventionVaccinations && count($InterventionVaccinations) > 0) ||
                    ($CarnetVaccinations && count($CarnetVaccinations) > 0) ||
                    ($VaccinPraticiens && count($VaccinPraticiens) > 0) ||
                    ($PatientVaccins && count($PatientVaccins) > 0))
                {
                    $delete = false;
                    $this->addFlash('error', 'Erreur de suprimé de cet élément !');
                }else{
                    $this->entityManager->remove($Vaccin);
                    $this->entityManager->flush();
                    $delete = true;
                    $this->addFlash('success', 'ville à été supprimé avec succès !');
                }
            }
        }

        return new JsonResponse(['form_delete' => $delete]);
    }

    /**
     * @Route("/admin/vaccin/edit-status", name="edit_status_vaccin", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function edit_status_vaccin(Request $request)
    {
        $idVaccin = $request->request->get('id_vaccin');
        $status = $request->request->get('status');

        $modif = false;

        if ($idVaccin != '' && $idVaccin != null){
            $etat = true;
            if ($status == 1){
                $etat = false;
            }
            $Vaccin = $this->vaccinRepository->find($idVaccin);
            $Vaccin->setEtat($etat);
            $this->entityManager->persist($Vaccin);
            $this->entityManager->flush();
            $modif = true;
            $this->addFlash('success', 'Enregistrement effectuée !');
        }
        return new JsonResponse(['form_edit' => $modif]);
    }
}
