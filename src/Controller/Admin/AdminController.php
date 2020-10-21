<?php

namespace App\Controller\Admin;

use App\Entity\CentreHealth;
use App\Entity\InterventionVaccination;
use App\Entity\State;
use App\Entity\TypeVaccin;
use App\Entity\Vaccin;
use App\Entity\VaccinCentreHealth;
use App\Form\CenterHealthType;
use App\Form\ChangePasswordType;
use App\Form\VaccinType;
use App\Repository\InterventionVaccinationRepository;
use App\Repository\OrdoConsultationRepository;
use App\Repository\OrdoVaccinationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\StateRepository;
use App\Repository\TypeVaccinRepository;
use App\Repository\UserRepository;
use App\Repository\VaccinCentreHealthRepository;
use App\Repository\VaccinRepository;
use App\Service\FileUploadService;
use App\Service\VaccinGenerate;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


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
    protected $userRepository;
    protected $entityManager;
    protected $ordoVaccinationRepository;
    protected $ordoConsultationRepository;
    protected $stateRepository;
    protected $interventionVaccinationRepository;

    function __construct(
        VaccinGenerate $vaccinGenerate,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        VaccinRepository $vaccinRepository,
        TypeVaccinRepository $typeVaccinRepository,
        InterventionVaccinationRepository $interventionVaccinationRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        OrdoVaccinationRepository $ordoVaccinationRepository,
        OrdoConsultationRepository $ordoConsultationRepository,
        StateRepository $stateRepository
    ) {
        $this->vaccinGenerate = $vaccinGenerate;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->vaccinRepository = $vaccinRepository;
        $this->typeVaccinRepository = $typeVaccinRepository;
        $this->userRepository = $userRepository;
        $this->interventionVaccinationRepository=$interventionVaccinationRepository;
        $this->entityManager = $entityManager;
        $this->ordoVaccinationRepository = $ordoVaccinationRepository;
        $this->ordoConsultationRepository = $ordoConsultationRepository;
        $this->stateRepository = $stateRepository;
    }
    /**
     * @Route("/", name="admin")
     */
    public function index()
    {
        $praticien = $this->praticienRepository->count(['etat' => 1]);
        $patient = $this->patientRepository->count(['etat' => 1]);
        $praticienReject = $this->praticienRepository->count(['etat' => 0]);
        $patientReject = $this->patientRepository->count(['etat' => 0]);
        $vacc = $this->ordoVaccinationRepository->count(['statusVaccin' => 1]);
        $cons = $this->ordoConsultationRepository->count(['statusConsultation' => 1]);
        $vaccInPr = $this->ordoVaccinationRepository->count(['statusVaccin' => 0]);
        $consInPr = $this->ordoConsultationRepository->count(['statusConsultation' => 0]);
        return $this->render('admin/dashboard.html.twig', [
            'praticien' => $praticien,
            'patient' => $patient,
            'prtRej' => $praticienReject,
            'patRej' => $patientReject,
            'vacc' => $vacc,
            'cons' => $cons,
            'vaccInPr' => $vaccInPr,
            'consInPr' => $consInPr,
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
        $all_rdv = $this->ordoVaccinationRepository->searchVaccin();
        return $this->render('admin/vaccinnation.html.twig', [
            'Vaccinations' => $all_rdv,
            'type' => 1
        ]);
    }

    /**
     * @Route("/consultation", name="consultation_admin")
     */
    public function consultation_admin()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('homepage');
        }
        $all_rdv = $this->ordoConsultationRepository->searchCons();

        return $this->render('admin/consultation.html.twig', [
            'consultations' => $all_rdv,
            'type' => 1
        ]);
    }

    /**
     * @Route("/intervention", name="intervention_admin")
     */
    public function intervention_admin(){
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('homepage');
        }
        $rdv = $this->interventionVaccinationRepository->searchintervention();
        return $this->render('admin/intervention.html.twig', [
            'intervention' => $rdv,
            'type' => 1
        ]);
    }

    /**
     * @Route("/chart/evolutions_des_vaccinations", name="evolutions_des_vaccinations", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function evolutions_des_vaccinations()
    {
        $evolut_vacc = $this->ordoVaccinationRepository->getQueryVacc();
        $evacc = [];
        if (count($evolut_vacc) > 0) {
            $i = 0;
            foreach ($evolut_vacc as $evolut_vac) {
                $evacc[$i]['x'] = $evolut_vac['year'] . '-' . $evolut_vac['month'] . '-01';
                $evacc[$i]['y'] = intval($evolut_vac['nb_vaccin']);
                $i++;
            }
        }

        return new JsonResponse($evacc);
    }


    /**
     * @Route("/chart/evolutions_des_patiens", name="evolutions_des_patiens", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function evolutions_des_patiens()
    {
        $evolut_patient = $this->patientRepository->findNbrPatientGroupByType();

        $epat = [];
        if (count($evolut_patient) > 0) {
            $i = 0;
            foreach ($evolut_patient as $evolut_pat) {
                $epat[$i]['label'] = $evolut_pat['typePatientName'];
                $epat[$i]['y'] = intval($evolut_pat['nb_patient']);
                $i++;
            }
        }

        return new JsonResponse($epat);
    }

    /**
     * @Route("/chart/evolutions_des_patients_praticiens", name="evolutions_des_patients_praticiens", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function evolutions_des_patients_praticiens()
    {
        $praticien = $this->praticienRepository->count(['etat' => 1]);
        $patient = $this->patientRepository->count(['etat' => 1]);

        $data = [];
        $data['patient'] = $patient;
        $data['praticien'] = $praticien;
        return new JsonResponse($data);
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

        $vaccin = $this->vaccinRepository->findBy([], ['vaccinName' => 'ASC']);
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
    public function register_vaccin(Request $request, TranslatorInterface $translator)
    {
        $vaccinRequest = $request->request->get('vaccin');

        $VaccinName = $vaccinRequest['vaccinName'];
        $TypeVaccin = $vaccinRequest['TypeVaccin'];
        $vaccinDescription = $vaccinRequest['vaccinDescription'];
        $datePriseInitiale = $vaccinRequest['datePriseInitiale'];
        $state = $vaccinRequest['state'];
        $state= $this->stateRepository->find($state);
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
        if (isset($vaccinRequest['etat'])) {
            $Status = true;
        }
        $TpVaccin = null;
        if ($TypeVaccin) {
            $TpVaccin = $this->typeVaccinRepository->find($TypeVaccin);
        }

        $idVaccin = $vaccinRequest['id'];
        if ($idVaccin != '' && $idVaccin != null) {
            $Vaccin = $this->vaccinRepository->find($idVaccin);
            $Vaccin->setVaccinName($VaccinName);
            $Vaccin->setTypeVaccin($TpVaccin);
            $Vaccin->setVaccinDescription($vaccinDescription);
            $Vaccin->setDatePriseInitiale($datePriseInitiale);
            $Vaccin->setState($state);
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
            $message = $translator->trans('modification successfully!');
            $this->addFlash('success', $message);
        } else {
            $VaccinNew = new Vaccin();
            $VaccinNew->setVaccinName($VaccinName);
            $VaccinNew->setTypeVaccin($TpVaccin);
            $VaccinNew->setVaccinDescription($vaccinDescription);
            $VaccinNew->setDatePriseInitiale($datePriseInitiale);
            $VaccinNew->setState($state);
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
            $message = $translator->trans('The city name has been registered successfully!');
            $this->addFlash('success', $message);
        }
        return $this->redirectToRoute("admin_vaccin");
    }

    /**
     * @Route("/upload-excel-vaccin", name="xlsx_import_vaccin")
     * @throws Exception
     */
    public function xlsx_import_vaccin(Request $request, FileUploadService $fileUploadService)
    {
        $fileFolder =  $this->getParameter('import_directory');
        $files = $request->files->get("file");
        $filePathName = $fileUploadService->upload($files);
        //$filePathName = md5(uniqid()) . $file->getClientOriginalName();
        if ($filePathName != null) {
            $spreadsheet = IOFactory::load($fileFolder . "/" . $filePathName); // Here we are able to read from the excel file
            $row = $spreadsheet->getActiveSheet()->removeRow(1); // I added this to be able to remove the first file line
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // here, the read data is turned into an array
            $i = 0;
            foreach ($sheetData as $Row) {
                if ($i != 0) {
                    $idVacc = $Row['A'];
                    $NomVaccin = $Row['B'];
                    $TypeVaccin = $Row['C'];
                    $Pays = $Row['D'];
                    $description = $Row['E'];
                    $Actif = $Row['F'];
                    $DatePrise = $Row['G'];
                    $statut = $Row['H'];
                    $state = null;
                    $tpVaccin = null;
                    $vaccine = null;

                    $city = null;
                    if ($Pays != null) {
                        $state = $this->stateRepository->findOneBy(['nameState' => $Pays]);
                        if ($state == null) {
                            $state = new State();
                            $state->setNameState($Pays);
                            $this->entityManager->persist($state);
                        }
                    }
                    if ($TypeVaccin != null) {
                        $tpVaccin = $this->typeVaccinRepository->findOneBy(['typeName' => $TypeVaccin]);
                        if ($tpVaccin == null) {
                            $tpVaccin = new TypeVaccin();
                            $tpVaccin->setTypeName($TypeVaccin);
                            $this->entityManager->persist($tpVaccin);
                        }
                    }

                    if ($NomVaccin != null) {
                        $vaccine = $this->vaccinRepository->findOneBy(['vaccinName' => $NomVaccin]);
                        if ($vaccine == null || $vaccine != null ) {
                            $act = $Actif == 1 ? true : false;
                            $vaccine = new Vaccin();
                            $vaccine->setIdVaccin($idVacc);
                            $vaccine->setVaccinName($NomVaccin);
                            $vaccine->setEtat($act);
                            $vaccine->setVaccinDescription($description);
                            $vaccine->setDatePrise($DatePrise);
                            $vaccine->setStatut($statut);
                            $vaccine->setTypeVaccin($tpVaccin);
                            $vaccine->setState($state);
                            $this->entityManager->persist($vaccine);
                            $this->entityManager->flush();
                        }
                    }
                }
                $i++;
            }
        }

        return new JsonResponse(['form_import' => true]);
    }

    private function variablesDate($args = "")
    {
        if (strpos($args, 'SEMAINE')  !== false) {
            return explode('SEMAINE', $args)[1] . " week";
        }
        if (strpos($args, 'MOIS')   !== false ||  strpos($args, 'GROSSESSE')  != false) {
            return explode('MOIS', $args)[1] . " month";
        }
        if (strpos($args, 'AN')  !== false) {
            return  explode('AN', $args)[1] . " year";
        }
    }

    /**
     * @Route("/vaccin/remove", name="remove_vaccin", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_vaccin(Request $request, TranslatorInterface $translator)
    {
        $idVaccin = $request->request->get('id_vaccin');
        $delete = false;
        if ($idVaccin != '' && $idVaccin != null) {
            $Vaccin = $this->vaccinRepository->find($idVaccin);
            if (null !=  $Vaccin) {
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
                    ($PatientVaccins && count($PatientVaccins) > 0)
                ) {
                    $message = $translator->trans('Error deleting this element!');
                    $delete = false;
                    $this->addFlash('error', $message);
                } else {
                    $this->entityManager->remove($Vaccin);
                    $this->entityManager->flush();
                    $message = $translator->trans('city has been successfully deleted!');
                    $delete = true;
                    $this->addFlash('success', $message);
                }
            }
        }

        return new JsonResponse(['form_delete' => $delete]);
    }

    /**
     * @Route("/vaccin/edit-status", name="edit_status_vaccin", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function edit_status_vaccin(Request $request, TranslatorInterface $translator)
    {
        $idVaccin = $request->request->get('id_vaccin');
        $status = $request->request->get('status');

        $modif = false;

        if ($idVaccin != '' && $idVaccin != null) {
            $etat = true;
            if ($status == 1) {
                $etat = false;
            }
            $Vaccin = $this->vaccinRepository->find($idVaccin);
            $Vaccin->setEtat($etat);
            $this->entityManager->persist($Vaccin);
            $this->entityManager->flush();
            $modif = true;
            $message = $translator->trans('Registration completed!');
            $this->addFlash('success', $message);
        }
        return new JsonResponse(['form_edit' => $modif]);
    }


    /**
     * @Route("/users/remove", name="remove_users", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_users(Request $request, TranslatorInterface $translator)
    {
        $id = $request->request->get('id');
        $type = $request->request->get('type');
        $delete = false;
        if ($type != '' && $type != null) {
            if ($type == 'patient') {
                $Patient = $this->patientRepository->find($id);
                if ($Patient && $Patient->getUser() != null) {
                    $this->entityManager->remove($Patient->getUser());
                }
                $this->entityManager->remove($Patient);
            } elseif ($type == 'praticien') {
                $Praticien = $this->praticienRepository->find($id);
                if ($Praticien && $Praticien->getUser() != null) {
                    $this->entityManager->remove($Praticien->getUser());
                }
                $this->entityManager->remove($Praticien);
            }
            $this->entityManager->flush();
            $delete = true;
            $message = $translator->trans('successfully removed!');
            $this->addFlash('success', $message);
        }

        return new JsonResponse(['form_delete' => $delete]);
    }

    /**
     * @Route("/users/edit-status", name="edit_status_users", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function edit_status_users(Request $request, TranslatorInterface $translator)
    {
        $id = $request->request->get('id');
        $type = $request->request->get('type');
        $status = $request->request->get('status');

        $Etat = false;
        if ($status == 0) {
            $Etat = true;
        }
        $modif = false;
        if ($type != '' && $type != null) {
            if ($type == 'patient') {
                $Patient = $this->patientRepository->find($id);
                $Patient->setEtat($Etat);
                if ($Patient && $Patient->getUser() != null) {
                    $User = $this->userRepository->find($Patient->getUser()->getId());
                    $User->setEtat($Etat);
                    $this->entityManager->persist($User);
                }
                $this->entityManager->persist($Patient);
            } elseif ($type == 'praticien') {
                $Praticien = $this->praticienRepository->find($id);
                $Praticien->setEtat($Etat);
                if ($Praticien && $Praticien->getUser() != null) {
                    $User = $this->userRepository->find($Praticien->getUser()->getId());
                    $User->setEtat($Etat);
                    $this->entityManager->persist($User);
                }
                $this->entityManager->persist($Praticien);
            }
            $this->entityManager->flush();
            $modif = true;
            $message = $translator->trans('Modification successfully!');
            $this->addFlash('success', $message);
        }

        return new JsonResponse(['form_status' => $modif]);
    }

    /**
     * @Route("/users/edit-password", name="show_change_password_users", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function show_change_password_users(Request $request)
    {
        $id = $request->request->get('id');
        $type = $request->request->get('type');

        $modif = false;
        $eventData = null;
        if ($type == 'patient') {
            $Patient = $this->patientRepository->find($id);
            if ($Patient && $Patient->getUser() != null) {
                //$User = $this->userRepository->find($Patient->getUser()->getId());
                $eventData = $Patient->getUser();
            }
        } elseif ($type == 'praticien') {
            $Praticien = $this->praticienRepository->find($id);
            if ($Praticien && $Praticien->getUser() != null) {
                //$User = $this->userRepository->find($Praticien->getUser()->getId());
                $eventData = $Praticien->getUser();
            }
        }

        $form = $this->createForm(ChangePasswordType::class, $eventData);
        $response = $this->renderView('security/_form_forget_password.html.twig', [
            'form' => $form->createView(),
            'user' => $eventData,
        ]);
        $form->handleRequest($request);
        return new JsonResponse(['form_edit_password' => $response]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @Route("/resetting-mdp", name="resetting_password")
     */
    public function resetting_password(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, TranslatorInterface $translator)
    {
        $requestUser = $request->request->get('change_password');
        $IdUser = $requestUser['id'];
        $PasswordUser = $requestUser['password']['first'];
        $user =  $this->userRepository->find($IdUser);
        if ($user) {
            $password = $userPasswordEncoder->encodePassword($user, $PasswordUser);
            $user->setPassword($password);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $message = $translator->trans('Password updated');
            $this->addFlash('success', $message);
        } else {
            $message = $translator->trans('Password change error');
            $this->addFlash('error', $message);
        }

        return $this->redirectToRoute('admin_member');
    }


    /**
     * @Route("/centre/health/{centre_id}", name="see_vaccin")
     */
    public function see_vaccin($centre_id, VaccinCentreHealthRepository $repository, Request $request)
    {
        $cv = $repository->findListVaccinsInCentre($centre_id);
        return $this->render('admin/centre_health/centre_vaccin.html.twig', [
            'centre' => $cv,
        ]);
    }

    /**
     * @Route("/modification/avatar", name="modif_avatar")
     */

    public function ModifPhoto(Request $request, FileUploadService $fileUploadService)
    {
        $user = $this->getUser();
        $users = $this->userRepository->find($user);
        $image = $request->files->get('images');
        if ($image != '') {
            $users->setPhoto($fileUploadService->upload($image, $this->getParameter('images_directory')));
            $this->entityManager->persist($users);
            $this->entityManager->flush();
        }

        return new JsonResponse(array("data" => "OK"));
    }
}
