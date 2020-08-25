<?php

namespace App\Controller\Api\Vaccination;

use App\Repository\OrdoConsultationRepository;
use App\Repository\OrdoVaccinationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class RetriveVaccination extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $authorizationChecker;
    private $tokenService;
    private $ordoVaccinationRepository;
    private $patientRepository;
    private $praticienRepository;

    public function __construct(
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenService $tokenService,
        OrdoVaccinationRepository $ordoVaccinationRepository,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository
    )
    {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenService = $tokenService;
        $this->ordoVaccinationRepository = $ordoVaccinationRepository;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
    }

    public function __invoke(Request $request)
    {
        if ($this->tokenService->getTokenStorage() == null) {
            return new JsonResponse(['status' => 'KO', 'message' => 'Vous étes deconnecter']);
        }
        $CurrentUser = $this->tokenService->getCurrentUser();

        if ($this->authorizationChecker->isGranted('ROLE_PATIENT')) {
            $patient = $this->patientRepository->findOneBy(['user' => $CurrentUser]);
            return $this->ordoVaccinationRepository->searchStatus($patient->getId(), 1);
        } elseif ($this->authorizationChecker->isGranted('ROLE_PRATICIEN')) {
            $praticien = $this->praticienRepository->findOneBy(['user' => $CurrentUser]);
            return $this->ordoVaccinationRepository->searchStatusPraticien($praticien->getId());
        } elseif ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->ordoVaccinationRepository->find(['statusVaccin' => 1]);
        }
    }
}