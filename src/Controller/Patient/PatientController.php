<?php

namespace App\Controller\Patient;

use App\Entity\Associer;
use App\Entity\Family;
use App\Entity\GroupFamily;
use App\Entity\IntervationConsultation;
use App\Entity\InterventionVaccination;
use App\Entity\OrdoConsultation;
use App\Entity\OrdoVaccination;
use App\Entity\PatientIntervationConsultation;
use App\Entity\PatientOrdoConsultation;
use App\Entity\PatientOrdoVaccination;
use App\Entity\Praticien;
use App\Form\RdvType;
use App\Form\VaccinType;
use App\Repository\AssocierRepository;
use App\Repository\CarnetVaccinationRepository;
use App\Repository\FamilyRepository;
use App\Repository\GroupFamilyRepository;
use App\Repository\InterventionVaccinationRepository;
use App\Repository\OrdoConsultationRepository;
use App\Repository\OrdonnaceRepository;
use App\Repository\OrdoVaccinationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\PropositionRdvRepository;
use App\Repository\UserRepository;
use App\Repository\VaccinRepository;
use App\Service\VaccinGenerate;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
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

    function __construct(
        VaccinGenerate $vaccinGenerate,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        OrdoConsultationRepository $ordoConsultationRepository,
        OrdoVaccinationRepository $ordoVaccinationRepository,
        PropositionRdvRepository $propositionRdvRepository,
        CarnetVaccinationRepository $carnetVaccinationRepository,
        VaccinRepository $vaccinRepository,
        InterventionVaccinationRepository $interventionVaccinationRepository,
        FamilyRepository $familyRepository,
        AssocierRepository $associerRepository,
        OrdonnaceRepository $ordonnaceRepository,
        UserRepository $userRepository,
        GroupFamilyRepository $groupFamilyRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->userRepository= $userRepository;
        $this->vaccinGenerate = $vaccinGenerate;
        $this->associerRepository = $associerRepository;
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
     * @Route("/rdv/valider", name="rdv_valider")
     */
    public function rdv_valider()
    {
        $user = $this->getUser();
        $patient= $this->patientRepository->findOneBy(['user'=>$user]);
        $rce = $this->ordoConsultationRepository->searchStatus($patient->getId(), 1);
        $proposition= $this->propositionRdvRepository->searchStatus($patient->getId(),1,1);
        $propos = $this->propositionRdvRepository->searchStat($patient->getId(),1,1);
        $intervention = $this->interventionVaccinationRepository->searchinterventionPatient($patient->getId(),1);
        return $this->render('patient/rdv_valider_patient.html.twig',[
            'consultation'=> $rce,
            'proposition'=>$proposition,
            'propos'=>$propos,
            'intervention'=>$intervention
        ]);
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
     * @Route("/rdv/rejected", name="rdv_annuler")
     */
    public function rdv_annuler()
    {
        $user = $this->getUser();
        $patient= $this->patientRepository->findOneBy(['user'=>$user]);
        $rce = $this->ordoConsultationRepository->searchStatus($patient->getId(), 2);
        $rve = $this->ordoVaccinationRepository->searchGe($patient->getId(), 2);
        $consu = $this->propositionRdvRepository->searchStat($patient->getId(),1,2);
        $con = $this->propositionRdvRepository->searchStatus($patient->getId(),1,2);
        $intervention = $this->interventionVaccinationRepository->searchinterventionPatient($patient->getId(), 2);
        return $this->render('patient/rdv_annuler_patient.html.twig',[
            'consultation'=> $rce,
            'vaccination'=>$rve,
            'intervention'=>$intervention,
            'vacc'=>$consu,
            'cons'=>$con,
        ]);
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
             "vaccination" => "VACCINATION",
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
        $rve = $this->ordoVaccinationRepository->searchGe($patient->getId());
        $cons= $this->propositionRdvRepository->searchStatus($patient->getId(),1,0);
        $vacc= $this->propositionRdvRepository->searchStat($patient->getId(),1,0);
        $intervention = $this->interventionVaccinationRepository->searchinterventionPatient($patient);

        $doctor = $this->praticienRepository->findAll();

        return $this->render('patient/rdv_patient.html.twig', [
            'consultation'=>$rce,
            'vaccination'=>$rve,
            'cons'=>$cons,
            'vacc'=>$vacc,
            'intervention'=>$intervention,
            'Doctors'=>$doctor,

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
    public function register_rdv(Request $request,TranslatorInterface $translator)
    {

        $rdvRequest = $request->request->get("rdv");
        $type = $rdvRequest["typeRdv"];
        $doctor = $rdvRequest["praticiens"];
        $vaccine = $rdvRequest["vaccin"];
        $vaccine = $this->vaccinRepository->find($vaccine);
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
        $patient =  $this->patientRepository->findOneBy(['user' => $user]);
        switch ($type){
            case 'consultation':
                if ($Id != ''){
                    $ordoconsultation = $this->ordoConsultationRepository->find($Id);
                }else{
                    $ordoconsultation = new OrdoConsultation();
                }

                $ordoconsultation->setDatePriseInitiale($Date_Rdv);
                $ordoconsultation->setObjetConsultation($description);
                $ordoconsultation->setStatusConsultation(0);
                $ordoconsultation->setEtat(0);
                $ordoconsultation->setPatient($patient);
                $ordoconsultation->setOrdonnance($ordo);
                $ordoconsultation->setStatusNotif(0);
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
            case 'vaccination':
                if ($Id != ''){
                    $ordovaccination = $this->ordoVaccinationRepository->find($Id);
                }else{
                    $ordovaccination = new OrdoVaccination();
                }
                $ordovaccination->setDatePrise($Date_Rdv);
                $ordovaccination->setOrdonnance($ordo);
                $ordovaccination->setReferencePraticienExecutant($praticien);
                $ordovaccination->setPatient($patient);
                $ordovaccination->setStatusVaccin(0);
                $ordovaccination->setEtat(0);
                $ordovaccination->setStatusNotif(0);
                $this->entityManager->persist($ordovaccination);
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
                    $inter = $this->interventionVaccinationRepository->find($Id);
                }else{
                    $inter = new InterventionVaccination();
                }
                $inter->setVaccin($vaccine);
                $inter->setPatient($patient);
                $inter->setStatusVaccin(0);
                $inter->setEtat(0);
                $inter->setDatePriseVaccin($Date_Rdv);
                $inter->setPraticienExecutant($praticien);
                $inter->setPraticienPrescripteur($praticien);
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
            "vaccination" => "VACCINATION",
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
            elseif ($rdv['typeRdv'] == 'vaccination'){
                $ordoCon = $this->ordoVaccinationRepository->find($rdv['id']);
                $rdv['vaccin'] = $ordoCon->getVaccin();
                $rdv['dateRdv'] = $ordoCon->getDatePrise();
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
     */
    public function check_association(Request $request, Praticien $praticien = null)
    {
        $user = $this->getUser();
        $data = 'KO';
        if ($praticien){
            $associate = $this->associerRepository->findOneBy(['patient' => $this->patientRepository->find($user->getId()), 'praticien' => $praticien]);
            if ($associate != null) $data = 'OK';

            return new JsonResponse(['status' => $data]);
        }
    }



}
