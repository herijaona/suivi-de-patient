<?php

namespace App\Controller\Api;

use App\Entity\Associer;
use App\Entity\CarnetVaccination;
use App\Entity\Family;
use App\Entity\GroupFamily;
use App\Entity\IntervationConsultation;
use App\Entity\InterventionVaccination;
use App\Entity\OrdoConsultation;
use App\Entity\OrdoVaccination;
use App\Entity\Praticien;
use App\Entity\PropositionRdv;
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
use App\Repository\TypePatientRepository;
use App\Repository\UserRepository;
use App\Repository\VaccinRepository;
use App\Service\TokenService;
use App\Service\VaccinGenerate;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;


class ApiController extends AbstractController
{
    protected $vaccinGenerate;
    protected $patientRepository;
    protected $praticienRepository;
    /**
     * @var FamilyRepository
     */
    protected $familyRepository;
    /**
     * @var GroupFamilyRepository
     */
    protected $groupFamilyRepository;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    function __construct(VaccinGenerate $vaccinGenerate,PatientRepository $patientRepository,PraticienRepository $praticienRepository,FamilyRepository $familyRepository,GroupFamilyRepository $groupFamilyRepository,EntityManagerInterface $entityManager)
    {
        $this->vaccinGenerate = $vaccinGenerate;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->familyRepository= $familyRepository;
        $this->groupFamilyRepository=$groupFamilyRepository;
        $this->entityManager= $entityManager;
    }

    /**
     * @Route("/apii", name="api_index")
     */
    public function index()
    {

        return new JsonResponse(['status' => 'OK']);
    }


    /**
     * @param PraticienRepository $praticienRepository
     * @return Response
     * @Route("/apip/posts", name="posts", methods={"GET"})
     */
  public function getPosts(PraticienRepository $praticienRepository){
    $data = $praticienRepository->findByPraticienUser(28);
    return $this->response($data);
   }

    /**
     * @param array $data
     * @return Response
     */
    public function response($data)
    {
        return new Response($this->get("serializer")->serialize($data, "json"));
    }


    /**
     * @Route ("/apip/praticien/rdv/in", name="apip_praticien_rdv_in", methods={"GET"})
     * @param TokenService $tokenService
     * @param OrdoConsultationRepository $ordoConsultationRepository
     * @param IntervationConsultationRepository $intervationConsultationRepository
     * @return JsonResponse
     */
    public function apip_praticien_rdv_in(TokenService $tokenService,OrdoConsultationRepository $ordoConsultationRepository, IntervationConsultationRepository $intervationConsultationRepository)
    {
        $user = $tokenService->getCurrentUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $ordo = $ordoConsultationRepository->searchStatusPraticien($praticien);
        $intervention = $intervationConsultationRepository->searchIn($praticien);
        $data = array_merge($ordo,$intervention);
        return new JsonResponse($data);
    }

    /**
     * @Route("/apip/patients/rdv/in", name="api_patients_rdv_in", methods={"GET"})
     * @param TokenService $tokenService
     * @param OrdoConsultationRepository $ordoConsultationRepository
     * @param OrdoVaccinationRepository $ordoVaccinationRepository
     * @param PropositionRdvRepository $propositionRdvRepository
     * @param InterventionVaccinationRepository $interventionVaccinationRepository
     * @return JsonResponse
     */
    public function api_patients_rdv_in(TokenService $tokenService, OrdoConsultationRepository $ordoConsultationRepository, OrdoVaccinationRepository $ordoVaccinationRepository, PropositionRdvRepository $propositionRdvRepository, InterventionVaccinationRepository $interventionVaccinationRepository)
    {
        $CurrentUser = $tokenService->getCurrentUser();

        $patient = $this->patientRepository->findOneBy(['user' => $CurrentUser]);
        $rce = $ordoConsultationRepository->searchStatus($patient->getId());
        $rve = $ordoVaccinationRepository->searchGe($patient->getId());
        $cons= $propositionRdvRepository->searchStatus($patient->getId(),1,0);
        $vacc= $propositionRdvRepository->searchStat($patient->getId(),1,0);
        $intervention = $interventionVaccinationRepository->searchinterventionPatient($patient);
        $data = array_merge($rce, $rve, $cons, $vacc, $intervention);

        return new JsonResponse(['results' => $data]);
    }

    /**
     * @Route ("/apip/patient/generation", name="apip_patient_generation", methods={"POST"})
     */
    public function apip_patient_generation(TokenService $tokenService, Request $request, OrdonnaceRepository $ordonnaceRepository){
        $user = $tokenService->getCurrentUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $generation = json_decode($request->getContent(),true);
        $praticien = $generation['praticien'];
        $praticien = $this->praticienRepository->find($praticien);
        $nom = $praticien->getLastName().' '. $praticien->getFirstName();
        $ordonnance = $ordonnaceRepository->findOneBy(['praticien'=>$praticien]);
        $ordovaccination = new OrdoVaccination();
        $ordovaccination->setOrdonnance($ordonnance);
        $ordovaccination->setPatient($patient);
        $ordovaccination->setStatusVaccin(0);
        $ordovaccination->setEtat(0);
        $this->entityManager->persist($ordovaccination);
        $this->entityManager->flush();
        return new JsonResponse("Votre demande de generation de vaccination est en attente dans le Praticien".$nom);

    }




    /**
     * @Route ("/api/add/membres", name="api_add_membres", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function api_add_membres(Request $request,UserRepository $userRepository)
    {
     $family = json_decode($request->getContent(),true);
     $username = $family['username'];
     $group = $family['id_group'];
     $user = $userRepository->findOneBy(['username'=>$username]);
     $patient = $this->patientRepository->findOneBy(['user'=>$user]);
     $groupe = $this->groupFamilyRepository->find($group);
     if ($this->familyRepository->findOneBy(['patientChild'=>$patient]) != null){return new JsonResponse("Cette patient est deja present dans une groupe famille");}
         $family = new Family();
         $family->setPatientChild($patient);
         $family->setGroupFamily($groupe);
         $family->setReferent(false);
         $this->entityManager->persist($family);
         $this->entityManager->flush();
         return new JsonResponse("Success ");

    }
    /**
     * @Route ("/api/cancel/rdv", name="api_cancel_rdv",methods={"POST"})
     */
    public function api_cancel_rdv(Request $request, OrdoConsultationRepository $ordoConsultationRepository,IntervationConsultationRepository $intervationConsultationRepository)
    {
        $rdv = json_decode($request->getContent(),true);
        $id = $rdv['id'];
        $type = $rdv['typeRdv'];
        if ($type == "consultation"){
            $ordo = $ordoConsultationRepository->find($id);
            $ordo->setStatusConsultation(2);
            $this->entityManager->persist($ordo);
            $this->entityManager->flush();
        }elseif ($type == "intervention")
        {
            $inter = $intervationConsultationRepository->find($id);
            $inter->setStatus(2);
            $this->entityManager->persist($inter);
            $this->entityManager->flush();
        }
        return new JsonResponse("Succès");

    }

    /**
     * @Route ("/apip/generate/vaccination", name="apip_generate_vaccination", methods={"POST"})
     * @param Request $request
     * @param VaccinGenerate $vaccinGenerate
     * @param OrdoVaccinationRepository $ordoVaccinationRepository
     */
    public function apip_generate_vaccination(Request $request, VaccinGenerate $vaccinGenerate, OrdoVaccinationRepository $ordoVaccinationRepository)
    {
        $vaccin = json_decode($request->getContent(),true);
        $id = $vaccin['id'];
        $patient = $vaccin['patient'];
        $patient =  $this->patientRepository->find($patient);
        $Date_Rdv= new DateTime('now');
        $ordoVacc = $ordoVaccinationRepository->find($id);
        if($ordoVacc != null){
            $vaccinGenerate->generateCalendar($patient, $Date_Rdv);
            $ordoVacc->setStatusVaccin(1);
            $this->entityManager->persist($ordoVacc);
            $this->entityManager->flush();
        }

    }


    /**
     * @Route ("/api/cancel/generation", name="api_cancel_generation",methods={"POST"})
     * @param Request $request
     * @param OrdoVaccinationRepository $ordoVaccinationRepository
     */
    public function api_cancel_generation(Request $request,OrdoVaccinationRepository $ordoVaccinationRepository){
        $vaccin = json_decode($request->getContent(),true);
        $id = $vaccin['id'];
        $ordoVacc = $ordoVaccinationRepository->find($id);
        if($ordoVacc != null)
        {
            $ordoVacc->setStatusVaccin(2);
            $this->entityManager->persist($ordoVacc);
            $this->entityManager->flush();
        }

    }


    /**
     * @Route ("/apip/add/rdv", name="apip_add_rdv", methods={"POST"})
     */
    public function apip_add_rdv(Request $request,IntervationConsultationRepository $intervationConsultationRepository,TokenService $tokenService,OrdonnaceRepository $ordonnaceRepository,OrdoConsultationRepository $ordoConsultationRepository)
    {
        $patient = $tokenService->getCurrentUser();
        $rdv = json_decode($request->getContent(),true);
        $praticien = $rdv['praticien'];
        $type = $rdv['typeRdv'];
        $description = $rdv['objet'];
        if ($praticien != null){
            $praticien = $this->praticienRepository->find($praticien);
            $ordonnance = $ordonnaceRepository->findOneBy(['praticien'=>$praticien]);
        }
        $patient = $this->patientRepository->findOneBy(['user'=>$patient]);
        switch ($type){
            case 'consultation':
                $ordoconsu = new OrdoConsultation();
                $ordoconsu->setObjetConsultation($description);
                $ordoconsu->setStatusConsultation(0);
                $ordoconsu->setEtat(0);
                $ordoconsu->setPatient($patient);
                $ordoconsu->setOrdonnance($ordonnance);
                $this->entityManager->persist($ordoconsu);
                $this->entityManager->flush();
                if (isset($rdv["Associer"])){
                    $assoc = new Associer();
                    $assoc->setPraticien($praticien);
                    $assoc->setPatient($patient);
                    $this->entityManager->persist($assoc);
                    $this->entityManager->flush();
                }
                break;
            case 'intervention':
                $inter = new IntervationConsultation();
                $inter->setPatient($patient);
                $inter->setStatus(0);
                $inter->setEtat(0);
                $inter->setObjetConsultation($description);
                $inter->setOrdonnace($ordonnance);
                $this->entityManager->persist($inter);
                $this->entityManager->flush();
                if (isset($rdv["Associer"])){
                    $assoc = new Associer();
                    $assoc->setPraticien($praticien);
                    $assoc->setPatient($patient);
                    $this->entityManager->persist($assoc);
                    $this->entityManager->flush();
                }
        }
        return new JsonResponse("Enregistrement du rendez-vous réussi ");
    }

    /**
     * @Route ("/api/country/fonction", name="api_country_fonction", methods={"POST"})
     * @param Request $request
     * @param FonctionRepository $fonctionRepository
     * @return JsonResponse
     */
    public function api_country_fonction(Request $request,FonctionRepository $fonctionRepository)
    {
        $praticien = json_decode($request->getContent(), true);
        $id_fonction = $praticien['id_fonction'];
        $fonction = $fonctionRepository->find($id_fonction);
        $country = $this->praticienRepository->searchcount($fonction);
        return new JsonResponse($country);

    }
    /**
     * @Route ("/api/city/fonction", name="api_city_fonction", methods={"POST"})
     */
    public function api_city_fonction(Request $request,FonctionRepository $fonctionRepository,StateRepository $stateRepository)
    {
        $city = json_decode($request->getContent(),true);
        $id_fonction = $city['id_fonction'];
        $country = $city['id_country'];
        $fonction = $fonctionRepository->find($id_fonction);
        $country = $stateRepository->find($country);
        $ci = $this->praticienRepository->searchcity($fonction, $country);
        return new JsonResponse($ci);
    }
    /**
     * @Route ("/api/praticien/fonction", name="api_praticien_fonction", methods={"POST"})
     */

    public function api_praticien_fonction(Request $request, FonctionRepository $fonctionRepository, StateRepository $stateRepository,CityRepository $cityRepository){
        $praticien = json_decode($request->getContent(),true);
        $id_fonction = $praticien['id_fonction'];
        $country = $praticien['id_country'];
        $city = $praticien['id_city'];
        $fonction = $fonctionRepository->find($id_fonction);
        $country = $stateRepository->find($country);
        $city = $cityRepository->find($city);
        $pra = $this->praticienRepository->searchpra($fonction, $country, $city);
        return new JsonResponse($pra);
    }

    /**
     * @Route ("/api/praticien/centre", name="api_praticien_centre", methods={"POST"})
     */
    public function api_praticien_centre(Request $request,CityRepository $cityRepository,CentreHealthRepository $centreHealthRepository){
        $city = json_decode($request->getContent(),true);
        $id= $city['id'];
        $c = $cityRepository->find($id);
        $centre = $centreHealthRepository->searchCentre($c);
        return new JsonResponse($centre);
    }

    /**
     * @Route ("/api/centre/praticien", name="api_centre_praticien", methods={"POST"})
     */
    public function api_centre_praticien(Request $request, CentreHealthRepository $centreHealthRepository, OrdonnaceRepository $ordonnaceRepository){
        $centre = json_decode($request->getContent(),true);
        $id = $centre['id'];
        $centre = $centreHealthRepository->find($id);
        $praticien = $ordonnaceRepository->searchPcent($centre);
        return new JsonResponse($praticien);
    }






    /**
     * @Route("/apip/patient/profile/edit", name="api_profile_edit", methods={"POST"})
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param CityRepository $cityRepository
     * @param StateRepository $stateRepository
     * @return JsonResponse
     */
    public function api_profile_edit(EntityManagerInterface $entityManager,Request $request,CityRepository $cityRepository, StateRepository  $stateRepository)
    {
        $patient = json_decode($request->getContent(), true);
        $id = $patient['id'];
        $cityBorn = $patient['cityBorn'];
        if ($cityBorn != null) $cityBorn = $cityRepository->find($cityBorn);
        $countryBorn = $patient['countryBorn'];
        if ($countryBorn != null) $countryBorn = $stateRepository->find($countryBorn);
        $address = $patient['address'];
        $nameState = $patient['nameState'];
        if ($nameState != null) $nameState = $stateRepository->find($nameState);
        $nameCity = $patient['nameCity'];
        if ($nameCity != null) $nameCity = $cityRepository->find($nameCity);
        $phone = $patient['phone'];
        $email = $patient['email'];
        $p = $this->patientRepository->find($id);
        $p->setAddress($address);
        $p->getUser()->setEmail($email);
        $p->setPhone($phone);
        $p->setCityOnBorn($cityBorn);
        $p->setCountryOnborn($countryBorn);
        $p->setCity($nameCity);
        $p->setState($nameState);
        $fatherName = $patient['fatherName'];
        $motherName = $patient['motherName'];
        if ($fatherName != null && $motherName != null){
            $p->setFatherName($fatherName);
            $p->setMotherName($motherName);
        }
        $entityManager->persist($p);
        $entityManager->flush();
        return new JsonResponse("ok");
    }


    /**
     * @Route ("/api/register/activate" , name="api_register_activate" , methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManager $entityManager
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function api_register_activate(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $code = json_decode($request->getContent(), true);
        $user = $userRepository->findOneBy(['activatorId'=>$code]);
        if ($user){
            $patient = $this->patientRepository->findOneBy(['user'=>$user]);
            $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
            if ($user->getEtat() != 1){
                $user->setEtat('1');
                $entityManager->persist($user);
                $entityManager->flush();
            }
            if ($patient && $patient->getEtat() != 1)
            {
                $patient->setEtat(true);
                $entityManager->persist($patient);
                $entityManager->flush();
            }
            if ($praticien && $praticien->getEtat() != 1)
            {
                $praticien->setEtat(true);
                $entityManager->persist($praticien);
                $entityManager->flush();
            }
            return new JsonResponse("Activation de votre compte");
        }else{
            return new JsonResponse("Code non valide");
        }


    }

    /**
     * @Route("/apip/register/group", name="apip_register_group",methods={"POST"})
     * @param TokenService $tokenService
     * @param Request $request
     * @return JsonResponse
     */
        public function apip_register_group(TokenService $tokenService,Request $request)
        {
            $CurrentUser = $tokenService->getCurrentUser();
            $groupe = json_decode($request->getContent(), true);
            $designation = $groupe['designation'];
            $patient = $this->patientRepository->findOneBy(['user'=>$CurrentUser]);
            $groupe_family = new GroupFamily();
            $groupe_family->setDesignation($designation);
            $groupe_family->setPatient($patient);
            $this->entityManager->persist($groupe_family);
            $family = new Family();
            $family->setGroupFamily($groupe_family);
            $family->setPatientChild($patient);
            $family->setReferent(true);
            $this->entityManager->persist($family);
            $this->entityManager->flush();
            return new JsonResponse("Succès de l'ajout de groupe de famille");
        }

    /**
     * @Route("/api/intervention", name="apip_intervention",methods={"POST"})
     */
    public function api_intervention(TokenService $tokenService,Request $request,OrdonnaceRepository $ordonnaceRepository,VaccinRepository $vaccinRepository,CarnetVaccinationRepository $carnetVaccinationRepository)
    {
        $intervention = json_decode($request->getContent(), true);
        $patient = $tokenService->getCurrentUser();
        $date = $intervention['date_prise'];
        $date_Rdv = new \DateTime($date);
        $praticien = $intervention['praticien'];
        $vaccin = $intervention['vaccin'];
        $id_carnet = $intervention['id_carnet'];
        $praticien = $this->praticienRepository->find($praticien);
        $ordonance = $ordonnaceRepository->findOneBy(['praticien'=>$praticien]);
        $patient = $this->patientRepository->find($patient);
        $vaccin = $vaccinRepository->find($vaccin);
        $carnet = $carnetVaccinationRepository->find($id_carnet);
        $inter = new InterventionVaccination();
        $inter->setEtat("0");
        $inter->setCarnet($carnet);
        $inter->setDatePriseVaccin($date_Rdv);
        $inter->setOrdonnace($ordonance);
        $inter->setPatient($patient);
        $inter->setVaccin($vaccin);
        $inter->setStatusVaccin("0");
        $this->entityManager->persist($inter);
        $this->entityManager->flush();
        return new JsonResponse("Envoie demande intervention");
        

    }


    /**
     * @Route("/api/check-etat",name="api_check_etat", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @return false|string|JsonResponse
     */
    public function api_check_etat(Request $request, UserRepository $userRepository)
    {
        $conexion = json_decode($request->getContent(), true);
        $username = $conexion['username'];
        $user = $userRepository->findOneBy(['username'=>$username]);
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        if ($patient){
            $etat = $patient->getUser()->getEtat();
            $mail = $patient->getUser()->getEmail();
        }elseif ($praticien){
            $etat = $praticien->getUser()->getEtat();
            $mail = $praticien->getUser()->getEmail();
        }
        $myObj = array($etat, $mail);
        return new JsonResponse($myObj);



    }



    /**
     * @Route("/apip/patient/profile", name="api_profile_patient", methods={"GET"})
     * @param TokenService $tokenService
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function api_profile_patient(TokenService $tokenService, UserRepository $userRepository)
    {
        $CurrentUser = $tokenService->getCurrentUser();
        $user = $userRepository->find($CurrentUser);
        $patient = $this->patientRepository->searchPatient($user);
        return new JsonResponse($patient);
    }

    /**
     * @Route("/apip/praticien/profile", name="api_profile_praticien" , methods={"GET"})
     * @param TokenService $tokenService
     * @param UserRepository $userRepository
     * @param FonctionRepository $fonctionRepository
     * @param OrdonnaceRepository $ordonnaceRepository
     * @return JsonResponse
     */
    public function api_profile_praticien(TokenService $tokenService, UserRepository $userRepository, FonctionRepository $fonctionRepository,OrdonnaceRepository $ordonnaceRepository)
    {
        $CurrentUser = $tokenService->getCurrentUser();
        $pr = $this->praticienRepository->findOneBy(['user'=>$CurrentUser]);
        $user = $userRepository->find($CurrentUser);
        $praticien = $this->praticienRepository->searchPr($user);
        $centre = $ordonnaceRepository->searchc($pr);
        $data = array_merge($praticien,$centre);
        return new JsonResponse(['profile' => $data]);
    }


    /**
     * @Route("/apip/patients/rdv/rejected", name="api_patients_rdv_rejected", methods={"GET"})
     * @param TokenService $tokenService
     * @param OrdoConsultationRepository $ordoConsultationRepository
     * @param OrdoVaccinationRepository $ordoVaccinationRepository
     * @param PropositionRdvRepository $propositionRdvRepository
     * @param InterventionVaccinationRepository $interventionVaccinationRepository
     * @return JsonResponse
     */
    public function api_patients_rdv_rejected(TokenService $tokenService, OrdoConsultationRepository $ordoConsultationRepository, OrdoVaccinationRepository $ordoVaccinationRepository, PropositionRdvRepository $propositionRdvRepository, InterventionVaccinationRepository $interventionVaccinationRepository)
    {
        $CurrentUser = $tokenService->getCurrentUser();

        $patient = $this->patientRepository->findOneBy(['user' => $CurrentUser]);
        $rce = $ordoConsultationRepository->searchStatus($patient->getId(), 2);
        $rve = $ordoVaccinationRepository->searchGe($patient->getId(), 2);
        $cons = $propositionRdvRepository->searchStat($patient->getId(),1,2);
        $con = $propositionRdvRepository->searchStatus($patient->getId(),1,2);
        $intervention = $interventionVaccinationRepository->searchinterventionPatient($patient->getId(), 2);
        $data = array_merge($rce, $rve, $cons, $con, $intervention);

        return new JsonResponse(['results' => $data]);
    }


    /**
     * @Route("/apip/patients/family", name="api_patients_family", methods={"GET"})
     */
    public function api_patients_family(TokenService $tokenService, GroupFamilyRepository $groupFamilyRepository, FamilyRepository $familyRepository)
    {
        $user = $tokenService->getCurrentUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        $my_group = [];
        $mygroups = $familyRepository->findBy(['patientChild' => $patient]);
            $m = 0;
            $id = 0;
            if($mygroups && count($mygroups) > 0){
                foreach ($mygroups as $mygroup){
                    $groupFamily  = $mygroup->getGroupFamily();
                    $id= $groupFamily->getId();
                    $my_group[$m]["ID"] = $groupFamily->getId();
                    $my_group[$m]["Name"] = $groupFamily->getDesignation();
                    $m++;
                }
            }
        $groupe = $groupFamilyRepository->find($id);
        $family = $familyRepository->searchFamily($groupe);

        $data = array($my_group,$family);
        return new JsonResponse($data);
    }
    /**
     * @Route ("/apip/praticien/vaccination", name="apip_praticien_vaccination", methods={"GET"})
     */
    public function apip_praticien_vaccination(TokenService $tokenService,OrdoVaccinationRepository $ordoVaccinationRepository, InterventionVaccinationRepository $interventionVaccinationRepository)
    {
        $user = $tokenService->getCurrentUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);

        $ordo = $ordoVaccinationRepository->searchStatusPraticien($praticien);
        $intervention = $interventionVaccinationRepository->searchIntCarnet($praticien);
        $data = array_merge($ordo, $intervention);
        return new JsonResponse($data);

    }


    /**
     * @Route("/apip/patients/vaccination", name="api_patients_vaccination", methods={"GET"})
     * @param TokenService $tokenService
     * @param CarnetVaccinationRepository $carnetVaccinationRepository
     * @return JsonResponse
     */
    public function api_patients_vaccination(TokenService  $tokenService, CarnetVaccinationRepository $carnetVaccinationRepository)
    {
        $CurrentUser = $tokenService->getCurrentUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$CurrentUser]);
        $carnet = $carnetVaccinationRepository->searchCarnet($patient);

        return new JsonResponse($carnet);

    }

    /**
     * @Route("/apip/patients/rdv", name="api_patients_rdv", methods={"GET"})
     * @param TokenService $tokenService
     * @param IntervationConsultationRepository $intervationConsultationRepository
     * @param OrdoConsultationRepository $ordoConsultationRepository
     * @return JsonResponse
     */
    public function api_patients_rdv(TokenService  $tokenService,IntervationConsultationRepository $intervationConsultationRepository,OrdoConsultationRepository $ordoConsultationRepository)
    {
        $CurrentUser = $tokenService->getCurrentUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$CurrentUser]);
        $consultation = $ordoConsultationRepository->searchStatus($patient);
        $intervention = $intervationConsultationRepository->searchStatusInter($patient);
        $rdv = array_merge($consultation, $intervention);
        return new JsonResponse($rdv);

    }

    /**
     * @Route("/apip/patients/praticien", name="api_patients_praticien", methods={"GET"})
     * @param TokenService $tokenService
     * @param AssocierRepository $associerRepository
     * @return JsonResponse
     */
    public function api_patients_praticien(TokenService  $tokenService,AssocierRepository $associerRepository)
    {
        $CurrentUser = $tokenService->getCurrentUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$CurrentUser]);
        $associer = $associerRepository->searchAssoc($patient);
        return new JsonResponse($associer);

    }

    /**
     * @Route("/apip/patients/rdv/accepted", name="api_patients_rdv_accepted", methods={"GET"})
     * @param TokenService $tokenService
     * @param OrdoConsultationRepository $ordoConsultationRepository
     * @param OrdoVaccinationRepository $ordoVaccinationRepository
     * @param PropositionRdvRepository $propositionRdvRepository
     * @param InterventionVaccinationRepository $interventionVaccinationRepository
     * @return JsonResponse
     */
    public function api_patients_rdv_accepted(TokenService $tokenService, OrdoConsultationRepository $ordoConsultationRepository, OrdoVaccinationRepository $ordoVaccinationRepository, PropositionRdvRepository $propositionRdvRepository, InterventionVaccinationRepository $interventionVaccinationRepository)
    {
        $CurrentUser = $tokenService->getCurrentUser();

        $patient = $this->patientRepository->findOneBy(['user' => $CurrentUser]);
        $rce = $ordoConsultationRepository->searchStatus($patient->getId(), 1);
        $proposition= $propositionRdvRepository->searchStatus($patient->getId(),1,1);
        $propos = $propositionRdvRepository->searchStat($patient->getId(),1,1);
        $intervention = $interventionVaccinationRepository->searchinterventionPatient($patient->getId(),1);
        $data = array_merge($rce, $proposition, $propos, $intervention);

        return new JsonResponse(['results' => $data]);
    }




    /**
     * @Route("/apip/patients/accept-proposition/{id}", name="api_patients_accept_proposition", methods={"GET"})
     * @param PropositionRdv $propositionRdv
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function api_patients_accept_proposition(PropositionRdv  $propositionRdv, EntityManagerInterface $entityManager)
    {
        if($propositionRdv){
            $propositionRdv->setStatusProposition(1);
            $entityManager->persist($propositionRdv);
            $entityManager->flush();
        }
        return new JsonResponse(['status' => 'OK']);
    }

    /**
     * @Route("/api/city", name="api_city" , methods={"GET"})
     */
    public function api_city(Request $request,StateRepository $stateRepository, CityRepository $cityRepository){
        $id = $request->get('id');
        $country = $stateRepository->find($id);
        $city = $cityRepository->searchCity($country);
        return new JsonResponse($city);
    }



    /**
     * @Route("/api/country", name="api_type_patient", methods={"GET"})
     * @param TypePatientRepository $typePatientRepository
     * @return JsonResponse
     */
    public function api_type_patient( StateRepository $stateRepository)
    {
        $state = $stateRepository->searchstate();
        return new JsonResponse($state);
    }

    /**
     * @Route("/api/fonction", name="api_fonction", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function api_fonction( FonctionRepository $fonctionRepository)
    {
        $fonction = $fonctionRepository->searchFonctions();
        return new JsonResponse($fonction);
    }




    /**
     * @Route("/apip/patients/proposition-rdv", name="api_patients_proposition_rdv", methods={"GET"})
     * @param TokenService $tokenService
     * @param PropositionRdvRepository $propositionRdvRepository
     * @return JsonResponse
     */
    public function api_patients_proposition_rdv(TokenService $tokenService, PropositionRdvRepository $propositionRdvRepository)
    {
        $CurrentUser = $tokenService->getCurrentUser();

        $patient = $this->patientRepository->findOneBy(['user' => $CurrentUser]);
        $propos = $propositionRdvRepository->searchProposition($patient);
        $pro = $propositionRdvRepository->searchPropositio($patient);
        $data = array_merge($pro, $propos);
        return new JsonResponse(['results' => $data]);
    }

    /**
     * @Route("/apip/patients/check-association/{praticien}", name="apip_patients_check_association", defaults={0})
     * @param Request $request
     * @param Praticien $praticien
     * @return JsonResponse
     */
    public function apip_patients_check_association( Praticien $praticien, TokenService $tokenService, AssocierRepository $associerRepository)
    {
        $CurrentUser = $tokenService->getCurrentUser();
        $data = 'KO';
        if ($praticien){
            $associate = $associerRepository->findOneBy(['patient' => $this->patientRepository->findOneBy(['user' => $CurrentUser]), 'praticien' => $praticien]);

            if ($associate != null) $data = 'OK';
        }
        return new JsonResponse(['status' => $data]);
    }


    /**
     * @Route(
     *     "api/family/{id}",
     *     name="delete_family",
     *     methods={"DELETE"},
     *     requirements={"id"="\d+"}
     * )
     * @param int $id
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        $family = $this->familyRepository->find($id);
        if ($family->getReferent() == 1){
            $id_groupe = $family->getGroupFamily()->getId();
            $groupe =  $this->groupFamilyRepository->findOneBy(['id'=>$id_groupe]);

            $this->entityManager->remove($family);
            $this->entityManager->flush();
            $this->entityManager->remove($groupe);
            $this->entityManager->flush();
        }
        $this->entityManager->remove($family);
        $this->entityManager->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }



    /**
     * @Route (
     *     "/api/see/calendar/{patient_id}",
     *     name="api_see_calendar",
     *     methods={"GET"},
     *     requirements={"patient_id"="\d+"}
     *     )
     * @param int $patient_id
     * @return JsonResponse
     */
    public function api_see_calendar($patient_id,CarnetVaccinationRepository $carnetVaccinationRepository)
    {
        $patient = $this->patientRepository->find($patient_id);
        $carnet = $carnetVaccinationRepository->findListVaccinsInCarnet($patient);
        return new JsonResponse($carnet);
    }


    /**
     * @Route(
     *     "/api/see/intervention/{carnet_id}",
     *     name="api_see_intervention",
     *     methods={"GET"},
     *     requirements={"carnet_id"="\d+"}
     * )
     * @param int $carnet_id
     *
     * @return JsonResponse
     */
    public function api_see_intervention($carnet_id,CarnetVaccinationRepository $carnetVaccinationRepository,InterventionVaccinationRepository $interventionVaccinationRepository)
    {
        $carnet =$carnetVaccinationRepository->find($carnet_id);
        $list = $interventionVaccinationRepository->searchInt($carnet);
        return new JsonResponse($list);
    }


    /**
     * @Route ("/apip/patient/associer", name="apip_patient_associer", methods={"GET"})
     * @param TokenService $tokenService
     * @param AssocierRepository $associerRepository
     * @return JsonResponse
     */

    public function apip_patient_associer(TokenService $tokenService, AssocierRepository $associerRepository)
    {
        $user = $tokenService->getCurrentUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $patient = $associerRepository->searcha($praticien);
        return new JsonResponse($patient);
    }
    /**
     * @Route("/apip/associer/patient", name="apip_associer_patient", methods={"GET"})
     *
     */
    public function apip_associer_patient(TokenService $tokenService,AssocierRepository $associerRepository)
    {
        $user = $tokenService->getCurrentUser();
        $pra= $this->praticienRepository->findOneBy(['user'=>$user]);
        $associer = $associerRepository->searchAssocier($pra);
        return new JsonResponse($associer);
    }
    /**
     * @Route ("/api/vaccin", name="api_vaccin", methods={"GET"})
     *
     */
    public function api_vaccin(VaccinRepository $vaccinRepository)
    {
        $vaccin = $vaccinRepository->vaccinDemande();
        return new JsonResponse($vaccin);

    }
    /**
     * @Route ("/apip/generate/praticien", name="apip_generate_praticien", methods={"POST"})
     */
    public function apip_generate_praticien(TokenService $tokenService,Request $request,VaccinRepository $vaccinRepository)
    {
        $user = $tokenService->getCurrentUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $gener = json_decode($request->getContent(), true);
        $patient = $gener['patient'];
        $patient = $this->patientRepository->find($patient);
        $vaccin = $gener['vaccin'];
        $vaccin = $vaccinRepository->find($vaccin);
        $identification = $gener['identification'];
        $date = $gener['date'];
        $heure = $gener['heure'];
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
        return new JsonResponse("Succès de l'enregistrement");
    }

    /**
     * @Route ("/api/organize/vaccination", name="api_organize_vaccination", methods={"POST"})
     */
    public function api_organize_vaccination(Request $request,InterventionVaccinationRepository $interventionVaccinationRepository, CarnetVaccinationRepository $carnetVaccinationRepository)
    {
        $organize = json_decode($request->getContent(), true);
        $id = $organize['id'];
        $date = $organize['date'];
        $heure = $organize['heure'];
        $carnet = $organize['carnet'];
        $rdv_date = str_replace("/", "-", $date);
        $Date_Rdv = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$heure)));
        $intervention = $interventionVaccinationRepository->find($id);
        $intervention->setDatePriseVaccin($Date_Rdv);
        $intervention->setStatusVaccin("1");
        $this->entityManager->persist($intervention);
        $this->entityManager->flush();
        $carnetvaccination = $carnetVaccinationRepository->find($carnet);
        $carnetvaccination->setStatus(1);
        $carnetvaccination->setDatePrise($Date_Rdv);
        $this->entityManager->persist($carnetvaccination);
        $this->entityManager->flush();
        return new JsonResponse("Organiser");

    }
    /**
     * @Route ("/apip/realize/vaccination", name="apip_realize_vaccination", methods={"POST"})
     */
    public function apip_realize_vaccination(TokenService $tokenService, Request $request,InterventionVaccinationRepository $interventionVaccinationRepository,CarnetVaccinationRepository $carnetVaccinationRepository)
    {
        $user= $tokenService->getCurrentUser();
        $praticien = $this->praticienRepository->findOneBy(['user'=>$user]);
        $realize = json_decode($request->getContent(), true);
        $id = $realize['id'];
        $lot = $realize['lot'];
        $carnet = $realize['carnet'];
        $inter = $interventionVaccinationRepository->find($id);
        $inter->setEtat("1");
        $this->entityManager->persist($inter);
        $this->entityManager->flush();
        $carnetvaccination = $carnetVaccinationRepository->find($carnet);
        $carnetvaccination->setEtat("1");
        $carnetvaccination->setPraticien($praticien);
        $carnetvaccination->setLot($lot);
        $this->entityManager->persist($carnetvaccination);
        $this->entityManager->flush();
        return new JsonResponse("Réaliser");

    }


}
