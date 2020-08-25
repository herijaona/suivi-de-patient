<?php


namespace App\Controller\Api;


use ApiPlatform\Core\Serializer\JsonEncoder;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DefaultController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;
    private $authorizationChecker;
    private $tokenService;
    private $patientRepository;
    private $praticienRepository;

    public function __construct(
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenService $tokenService,
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository
    )
    {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenService = $tokenService;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
    }

    public function CurrentUserCheck(Request $request){
        if ($this->tokenService->getTokenStorage() == null) {
            return new JsonResponse(['status' => 'KO', 'message' => 'Vous étes deconnecter']);
        }
        $CurrentUser = $this->tokenService->getCurrentUser();
        $data = null;
        if ($this->authorizationChecker->isGranted('ROLE_PATIENT')) {
            $data = $this->patientRepository->findOneBy(['user' => $CurrentUser]);
        } elseif ($this->authorizationChecker->isGranted('ROLE_PRATICIEN')) {
            $data = $this->praticienRepository->findOneBy(['user' => $CurrentUser]);
        }
        return new JsonResponse(['status' => 'OK', 'data' => $data->getId()]);
    }

}