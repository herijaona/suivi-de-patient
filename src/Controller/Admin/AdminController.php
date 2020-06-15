<?php

namespace App\Controller\Admin;

use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Service\VaccinGenerate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
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
     * @Route("/", name="admin")
     */
    public function index()
    {
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);

    }

    /**
     * @Route("/vaccin", name="vaccin_admin")
     */
    public function vaccin_admin()
    {
        return $this->render('admin/vaccin.html.twig', [
            'controller_name' => 'AdminController',
        ]);

    }

    /**
     * @Route("/praticien", name="praticiens_admin")
     */
    public function praticiens_admin()
    {

        $praticien = $this->praticienRepository->findByPraticien();

        return $this->render('admin/praticien.html.twig', [
            'praticiens' => $praticien,
        ]);

    }

    /**
     * @Route("/patient", name="patients_admin")
     */
    public function patients_admin()
    {
        $patient = $this->patientRepository->findByPatient();
        return $this->render('admin/patient.html.twig', [
            'patients' => $patient,
        ]);

    }
}
