<?php


namespace App\Service;


use App\Repository\PatientRepository;
use App\Repository\TypePatientRepository;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Security;

class StatsService
{

    private $userRepository;
    private $patientRepository;
    private $typePatientRepository;
    private $container;
    private $security;

    public function __construct
    (
        ContainerInterface $container,
        TypePatientRepository $typePatientRepository,
        PatientRepository $patientRepository,
        UserRepository $userRepository,
        Security $security
    )
    {
        $this->userRepository = $userRepository;
        $this->patientRepository = $patientRepository;
        $this->typePatientRepository = $typePatientRepository;
        $this->container = $container;
        $this->security = $security;
    }

    public function getTypePatient() {
        if($this->security->getUser())
            return $this->patientRepository->findOneBy(['user' => $this->security->getUser()])->getTypePatient();
        else
            return null;
    }


}