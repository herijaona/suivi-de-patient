<?php

namespace App\Controller\Api;

use App\Entity\Praticien;
use App\Entity\PropositionRdv;
use App\Repository\AssocierRepository;
use App\Repository\IntervationConsultationRepository;
use App\Repository\InterventionVaccinationRepository;
use App\Repository\OrdoConsultationRepository;
use App\Repository\OrdoVaccinationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\PropositionRdvRepository;
use App\Repository\UserRepository;
use App\Service\TokenService;
use App\Service\VaccinGenerate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ApiController extends AbstractController
{
    protected $vaccinGenerate;
    protected $patientRepository;
    protected $praticienRepository;

    function __construct(VaccinGenerate $vaccinGenerate,PatientRepository $patientRepository,PraticienRepository $praticienRepository)
    {
        $this->vaccinGenerate = $vaccinGenerate;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
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
     * @return JsonResponse
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

}
