<?php

namespace App\Controller\Praticien;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PraticienController extends AbstractController
{
    /**
     * @Route("/praticien", name="praticien")
     */
    public function praticien()
    {
        /*if (!$this->isGranted('ROLE_PRATICIEN')) {
            return $this->redirectToRoute('homepage');
        }*/
        return $this->render('praticien/praticien.html.twig', [
            'controller_name' => 'PraticienController',
        ]);
    }

}
