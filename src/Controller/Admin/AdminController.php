<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
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
        return $this->render('admin/praticien.html.twig', [
            'controller_name' => 'AdminController',
        ]);

    }

    /**
     * @Route("/patient", name="patients_admin")
     */
    public function patients_admin()
    {
        return $this->render('admin/patient.html.twig', [
            'controller_name' => 'AdminController',
        ]);

    }
}
