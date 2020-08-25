<?php


namespace App\Controller\Api\Vaccination;


use App\Entity\OrdoConsultation;
use App\Entity\OrdoVaccination;
use App\Repository\OrdoConsultationRepository;
use App\Repository\OrdonnaceRepository;
use App\Repository\OrdoVaccinationRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\VaccinRepository;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CreateVaccination extends AbstractController
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

    public function __construct(
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenService $tokenService,
        OrdoVaccinationRepository $ordoVaccinationRepository,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        OrdonnaceRepository $ordonnaceRepository,
        VaccinRepository $vaccinRepository,
        OrdoConsultationRepository $ordoConsultationRepository
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
    }


}