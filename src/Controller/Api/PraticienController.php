<?php


namespace App\Controller\Api;


use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Service\VaccinGenerate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PraticienController  extends AbstractController
{
    protected $vaccinGenerate;
    protected $patientRepository;
    protected $praticienRepository;

    function __construct(VaccinGenerate $vaccinGenerate, PatientRepository $patientRepository, PraticienRepository $praticienRepository)
    {
        $this->vaccinGenerate = $vaccinGenerate;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
    }


}