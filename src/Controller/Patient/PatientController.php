<?php

namespace App\Controller\Patient;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PatientController extends AbstractController
{
    /**
     * @Route("/patient", name="patient")
     */
    public function patient()
    {
        return $this->render('patient/patient.html.twig', [
            'controller_name' => 'PatientController',
        ]);
    }

}
