<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    /**
     * @Route("/admin/city", name="admin_city")
     */
    public function index()
    {
        return $this->render('admin/city/index.html.twig', [
            'controller_name' => 'CityController',
        ]);
    }
}
