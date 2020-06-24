<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TypeVaccinController extends AbstractController
{
    /**
     * @Route("/admin/type/vaccin", name="admin_type_vaccin")
     */
    public function index()
    {
        return $this->render('admin/type_vaccin/index.html.twig', [
            'controller_name' => 'TypeVaccinController',
        ]);
    }
}
