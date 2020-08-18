<?php


namespace App\Controller\Api\Vaccination;


use App\Entity\OrdoConsultation;
use App\Entity\OrdoVaccination;
use App\Entity\PropositionRdv;
use App\Repository\OrdoConsultationRepository;
use App\Repository\OrdonnaceRepository;
use App\Repository\OrdoVaccinationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\PropositionRdvRepository;
use App\Repository\VaccinRepository;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class VaccinationController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    private $authorizationChecker;
    private $tokenService;
    private $ordoVaccinationRepository;
    private $patientRepository;
    private $praticienRepository;
    private $ordonnaceRepository;
    private $vaccinRepository;
    private $ordoConsultationRepository;
    private $propositionRdvRepository;

    public function __construct(
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenService $tokenService,
        OrdoVaccinationRepository $ordoVaccinationRepository,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        OrdonnaceRepository $ordonnaceRepository,
        VaccinRepository $vaccinRepository,
        OrdoConsultationRepository $ordoConsultationRepository,
        PropositionRdvRepository $propositionRdvRepository
    )
    {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenService = $tokenService;
        $this->ordoVaccinationRepository = $ordoVaccinationRepository;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->ordonnaceRepository = $ordonnaceRepository;
        $this->vaccinRepository = $vaccinRepository;
        $this->ordoConsultationRepository = $ordoConsultationRepository;
        $this->propositionRdvRepository = $propositionRdvRepository;
    }

    public function inProgressAction(Request $request){
        if ($this->tokenService->getTokenStorage() == null) {
            return new JsonResponse(['status' => 'KO', 'message' => 'Vous étes deconnecter']);
        }
        $CurrentUser = $this->tokenService->getCurrentUser();
        $data = [];
        if ($this->authorizationChecker->isGranted('ROLE_PATIENT')) {
            $patient = $this->patientRepository->findOneBy(['user' => $CurrentUser]);
            $data = $this->ordoVaccinationRepository->searchStatus($patient->getId(), 0);
        } elseif ($this->authorizationChecker->isGranted('ROLE_PRATICIEN')) {
            $praticien = $this->praticienRepository->findOneBy(['user' => $CurrentUser]);
            $data = $this->ordoVaccinationRepository->searchStatusPraticienEnValid($praticien->getId());
        } elseif ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $data = $this->ordoVaccinationRepository->find(['statusVaccin' => 0]);
        }

        return new JsonResponse($data);
    }

    public function inRejectedAction(Request $request){
        if ($this->tokenService->getTokenStorage() == null) {
            return new JsonResponse(['status' => 'KO', 'message' => 'Vous étes deconnecter']);
        }
        $CurrentUser = $this->tokenService->getCurrentUser();
        $data = [];
        if ($this->authorizationChecker->isGranted('ROLE_PATIENT')) {
            $patient = $this->patientRepository->findOneBy(['user' => $CurrentUser]);
            $data = $this->ordoVaccinationRepository->searchStatus($patient->getId(), 2);
        } elseif ($this->authorizationChecker->isGranted('ROLE_PRATICIEN')) {
            $praticien = $this->praticienRepository->findOneBy(['user' => $CurrentUser]);
            $data = $this->ordoVaccinationRepository->searchStatusPraticien($praticien->getId(), 2, 0);
        } elseif ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $data = $this->ordoVaccinationRepository->find(['statusVaccin' => 2]);
        }
        return new JsonResponse($data);
    }


    public  function registerRdvAction(Request $request){
        $rdvRequest = json_decode($request->getContent(),true);
        $type = $rdvRequest["typeRdv"];
        $doctor = $rdvRequest["praticiens"];
        $vaccin = $rdvRequest["vaccin"];
        $date = $rdvRequest["dateRdv"];
        $description = $rdvRequest["description"];
        $heure = $rdvRequest["heureRdv"];
        $Id = $rdvRequest["id"];

        $user = $this->tokenService->getCurrentUser();

        $rdv_date = str_replace("/", "-", $date);

        $Date_Rdv = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$heure)));

        $ordo = null;
        $vaccination = null;
        $praticien = null;

        if($doctor != ''){
            $praticien =  $this->praticienRepository->find($doctor);
            $ordo = $this->ordonnaceRepository->findOneBy(['praticien' => $praticien]);
        }

        if($vaccin != ''){
            $vaccination = $this->vaccinRepository->find($vaccin);
        }

        $patient =  $this->patientRepository->findOneBy(['user' => $user]);

        if ($type=="consultation"){
            if ($Id != ''){
                $ordoconsultation = $this->ordoConsultationRepository->find($Id);
            }else{
                $ordoconsultation = new OrdoConsultation();
            }
            $ordoconsultation->setDateRdv($Date_Rdv);
            $ordoconsultation->setObjetConsultation($description);
            $ordoconsultation->setStatusConsultation(0);
            $ordoconsultation->setEtat(0);
            $ordoconsultation->setPatient($patient);
            $ordoconsultation->setOrdonnance($ordo);
            $this->em->persist($ordoconsultation);
            $this->em->flush();
        }else{
            if ($Id != ''){
                $ordovaccination = $this->ordoVaccinationRepository->find($Id);
            }else{
                $ordovaccination = new OrdoVaccination();
            }
            $ordovaccination->setDatePrise($Date_Rdv);
            $ordovaccination->setOrdonnance($ordo);
            $ordovaccination->setReferencePraticienExecutant($praticien);
            $ordovaccination->setVaccin($vaccination);
            $ordovaccination->setPatient($patient);
            $ordovaccination->setStatusVaccin(0);
            $ordovaccination->setEtat(0);
            $this->em->persist($ordovaccination);
            $this->em->flush();
        }
        return new JsonResponse(["status" => "OK"]);
    }

    public  function updateStatusRdvAction(Request $request)
    {
        $rdvRequest = json_decode($request->getContent(),true);

        switch ($rdvRequest['type']){
            case 'vaccination':
                $ordoVacc = $this->ordoVaccinationRepository->find($rdvRequest['id']);
                if ($rdvRequest['action'] == 'active'){
                    $ordoVacc->setStatusVaccin(1);
                    $this->em->persist($ordoVacc);
                    $this->em->flush();
                }
                else{
                    $ordoVacc->setStatusVaccin(2);
                    $this->em->persist($ordoVacc);
                    $this->em->flush();
                }
                break;
            case 'consultation':
                $ordoConsu = $this->ordoConsultationRepository->find($rdvRequest['id']);
                if ($rdvRequest['action'] == 'active'){
                    $ordoConsu->setstatusConsultation(1);
                    $this->em->persist($ordoConsu);
                    $this->em->flush();
                }
                else{
                    $ordoConsu->setstatusConsultation(2);
                    $this->em->persist($ordoConsu);
                    $this->em->flush();
                }
                break;
        }
        return new JsonResponse(['status' => 'OK']);
    }

    public  function register_proposition(Request $request)
    {

        $propositionRequest = json_decode($request->getContent(),true);

        $description = $propositionRequest["description"];
        $patient = $propositionRequest["patient"];
        $date = $propositionRequest["dateRdv"];
        $heure = $propositionRequest["heureRdv"];
        $Id = $propositionRequest["id"];

        $user = $this->tokenService->getCurrentUser();
        $rdv_date = str_replace("/", "-", $date);
        $Date_Rdv = new \DateTime(date ("Y-m-d H:i:s", strtotime ($rdv_date.' '.$heure)));
        $praticien= $this->praticienRepository->findOneBy(['user'=>$user]);
        if($patient != ''){
            $patient =  $this->patientRepository->find($patient);

        }

        if($Id !='' && $Id = 0)
        {
            $proposition = $this->propositionRdvRepository->find($Id);
        }else {
            $proposition = new PropositionRdv();
        }

        $proposition->setDescriptionProposition($description);
        $proposition->setDateProposition($Date_Rdv);
        $proposition->setPraticien($praticien);
        $proposition->setPatient($patient);
        $proposition->setStatusProposition(0);
        $proposition->setEtat(0);
        $proposition->setStatusNotif(0);
        $this->em->persist($proposition);
        $this->em->flush();

        return new JsonResponse(['status' => 'OK']);
    }
}