<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RegionController extends AbstractController
{
    /**
     * @Route("/admin/region", name="admin_region")
     */
    public function index()
    {
        return $this->render('admin/region/index.html.twig', [
            'controller_name' => 'RegionController',
        ]);
    }
}
