<?php


namespace App\Controller\Api\Consultation;


use App\Entity\Family;
use App\Entity\GroupFamily;
use App\Entity\IntervationConsultation;
use App\Entity\Patient;
use App\Entity\PatientIntervationConsultation;
use App\Repository\FamilyRepository;
use App\Repository\GroupFamilyRepository;
use App\Repository\OrdoConsultationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\PropositionRdvRepository;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConsultationController extends  AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var TokenService
     */
    private $tokenService;
    /**
     * @var OrdoConsultationRepository
     */
    private $ordoConsultationRepository;
    /**
     * @var PatientRepository
     */
    private $patientRepository;
    /**
     * @var PraticienRepository
     */
    private $praticienRepository;
    /**
     * @var GroupFamilyRepository
     */
    private $groupFamilyRepository;
    /**
     * @var FamilyRepository
     */
    private $familyRepository;
    /**
     * @var PropositionRdvRepository
     */
    private $propositionRdvRepository;

    /**
     * ConsultationController constructor.
     * @param EntityManagerInterface $em
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenService $tokenService
     * @param OrdoConsultationRepository $ordoConsultationRepository
     * @param PatientRepository $patientRepository
     * @param PraticienRepository $praticienRepository
     * @param GroupFamilyRepository $groupFamilyRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenService $tokenService,
        OrdoConsultationRepository $ordoConsultationRepository,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        GroupFamilyRepository $groupFamilyRepository,
        FamilyRepository $familyRepository,
        PropositionRdvRepository $propositionRdvRepository
    )
    {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenService = $tokenService;
        $this->ordoConsultationRepository = $ordoConsultationRepository;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->groupFamilyRepository = $groupFamilyRepository;
        $this->familyRepository = $familyRepository;
        $this->propositionRdvRepository = $propositionRdvRepository;
    }


    /**
     * @Route("/apip/ordoconsultation_in_progress", name="api_ordoconsultation_in_progress", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function inProgressAction(Request $request){

        if ($this->tokenService->getTokenStorage() == null) {
            return new JsonResponse(['status' => 'KO', 'message' => 'Vous étes deconnecter']);
        }
        $CurrentUser = $this->tokenService->getCurrentUser();
        $data = [];

        if ($this->authorizationChecker->isGranted('ROLE_PATIENT')) {
            $patient = $this->patientRepository->findOneBy(['user' => $CurrentUser]);
            $data = $this->ordoConsultationRepository->searchStatus($patient->getId(), 0);
        } elseif ($this->authorizationChecker->isGranted('ROLE_PRATICIEN')) {
            $praticien = $this->praticienRepository->findOneBy(['user' => $CurrentUser]);
            $data = $this->ordoConsultationRepository->searchStatusPraticienEnValid($praticien->getId());
        } elseif ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $data = $this->ordoConsultationRepository->findBy(['statusVaccin' => 0]);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/apip/ordoconsultation_rejected", name="api_ordoconsultation_rejected", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function inRejectedAction(Request $request){
        if ($this->tokenService->getTokenStorage() == null) {
            return new JsonResponse(['status' => 'KO', 'message' => 'Vous étes deconnecter']);
        }
        $CurrentUser = $this->tokenService->getCurrentUser();
        $data = [];
        if ($this->authorizationChecker->isGranted('ROLE_PATIENT')) {
            $patient = $this->patientRepository->findOneBy(['user' => $CurrentUser]);
            $data = $this->ordoConsultationRepository->searchStatus($patient->getId(), 2);
        } elseif ($this->authorizationChecker->isGranted('ROLE_PRATICIEN')) {
            $praticien = $this->praticienRepository->findOneBy(['user' => $CurrentUser]);
            $data = $this->ordoConsultationRepository->searchStatusPraticien($praticien->getId(), 2, 0);
        } elseif ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $data = $this->ordoConsultationRepository->findBy(['statusVaccin' => 2]);
        }
        return new JsonResponse($data);
    }

    /**
     * @Route("/apip/register_group_patient", name="register_group_patient", methods={"POST"})
     * @param Request $request
     */
    public function register_group(Request $request)
    {
        $groupRequest = json_decode($request->getContent(),true);

        $group_id = in_array("group_id", $groupRequest, TRUE)? $groupRequest['group_id'] : '';
        $group_name = in_array("group_name", $groupRequest, TRUE)? $groupRequest['group_name'] : '';

        $user = $this->tokenService->getCurrentUser();

        $patient = $this->patientRepository->findOneBy(['user'=> $user]);
        if ($group_id != ""){
            $group_family = $this->groupFamilyRepository->find($group_id);
            $group_family->setDesignation($group_name);
            $this->em->persist($group_family);
            $this->em->flush();
        }else{
            $group_family = new GroupFamily();
            $group_family->setDesignation($group_name);
            $group_family->setPatient($patient);
            $this->em->persist($group_family);
            $family = new Family();
            $family->setGroupFamily($group_family);
            $family->setPatientChild($patient);
            $this->em->persist($family);
            $this->em->flush();
        }
        return new JsonResponse(['status' => 'OK']);
    }

    /**
     * @Route("/apip/add_new_membres_group", name="add_new_membres_group_patients", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add_new_membres_group(Request $request)
    {
        $groupRequest = json_decode($request->getContent(),true);

        $group_id = in_array("group_id", $groupRequest, TRUE)? $groupRequest['group_id'] : '';
        $group_patient = in_array("patient", $groupRequest, TRUE)? $groupRequest['patient'] : '';

        if ($group_id != ""){
            $group_family = $this->groupFamilyRepository->find($group_id);
            $patient = $this->patientRepository->find($group_patient);
            $family = new Family();
            $family->setGroupFamily($group_family);
            $family->setPatientChild($patient);
            $this->em->persist($family);
            $this->em->flush();
        }
        return new JsonResponse(['status' => 'OK']);
    }

    /**
     * @Route("/apip/delete_membres_in_group/{id}/{membre}", name="delete_membres_group_patients", methods={"DELETE"})
     */
    public function delete_membres_group(GroupFamily $id, Patient $membre)
    {
        if ($id != null && $membre != null){
            $group_family = $this->familyRepository->findOneBy(['patientChild' => $membre, 'groupFamily' => $id]);

            if($group_family){
                $this->em->remove($group_family);
                $this->em->flush();
            }
        }
        return new JsonResponse(['status' => 'OK']);
    }

    /**
     * @Route("/apip/proposition_acccepted_reject", name="api_proposition_accepted", methods={"POST"})
     */
    public function proposition_accepted_rejected(Request $request)
    {
        $parRequest = json_decode($request->getContent(),true);

        $id = in_array("id", $parRequest, TRUE)? $parRequest['id'] : '';
        $type = in_array("type", $parRequest, TRUE)? $parRequest['type'] : '';
        switch ($type){
            case 'accepted':
                if($id != '')
                {
                    $proposition = $this->propositionRdvRepository->find($id);
                    $proposition->setStatusProposition(1);
                    $this->em->persist($proposition);
                    $interCons = new IntervationConsultation();
                    $interCons->setPatient($proposition->getPatient());
                    $interCons->setPraticienPrescripteur($proposition->getPraticien());
                    $interCons->setDateConsultation($proposition->getDateProposition());
                    $interCons->setPraticienConsultant($proposition->getPraticien());
                    $interCons->setEtat(0);
                    $interCons->setProposition($proposition);
                    $this->em->persist($interCons);
                    $this->em->flush();
                }
                break;
            case 'rejected':
                if($id != '')
                {
                    $proposition = $this->propositionRdvRepository->find($id);
                    $proposition->setStatusProposition(2);
                    $this->em->persist($proposition);
                    $this->em->flush();
                }
                break;
        }

        return new JsonResponse(['status' => 'OK']);
    }
}