<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/homepage", name="homepage")
     */
    public function index()
    {
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }

    /**
     * @Route("/check-users", name="check_users")
     */
    public function check_users()
    {
        if ($this->isGranted('ROLE_PATIENT')) {
            return $this->redirectToRoute('patient');
        }
        elseif ($this->isGranted('ROLE_PRATICIEN')){
            return $this->redirectToRoute('praticien');
        }
    }
}
