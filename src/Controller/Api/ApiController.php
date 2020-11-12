<?php

namespace App\Controller\Api;

use App\Entity\Family;
use App\Entity\GroupFamily;
use App\Entity\InterventionVaccination;
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
     * @Route ("/apip/intervention/accept", name="apip_intervention_accept", methods={"GET"})
     */
    public function apip_intervention_accept(Request $request)
    {
     $intervetion = json_decode($request->getContent(),true);
     $date = $intervetion['date'];
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



}
