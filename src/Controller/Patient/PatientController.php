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
        if (!$this->isGranted('ROLE_PATIENT')) {
            return $this->redirectToRoute('homepage');
        }
        return $this->render('patient/patient.html.twig', [
            'controller_name' => 'PatientController',
        ]);
    }

}
