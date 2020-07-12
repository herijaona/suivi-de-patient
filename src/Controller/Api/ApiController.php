<?php

namespace App\Controller\Api;

use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\UserRepository;
use App\Service\VaccinGenerate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
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
     * @Route("/", name="api_index")
     */
    public function index()
    {

        return new JsonResponse(['status' => 'OK']);
    }


    /**
     * @Route("/praticiens", name="api_praticiens")
     */
    public function api_praticiens()
    {
        //$praticien = $this->praticienRepository->findByPraticien();

        $data = $this->praticienRepository->findAll();
        return  $this->praticienRepository->findByPraticien();
    }
    /**
     * @Route("/patients", name="api_patients")
     */
    public function api_patients()
    {
        $patients = $this->patientRepository->findByPatient();
        return new JsonResponse(['patients' => $patients]);
    }


    /**
     * @param array $data
     * @return Response
     */
    public function response($data)
    {
        return new Response($this->get("serializer")->serialize($data, "json"));
    }

}
