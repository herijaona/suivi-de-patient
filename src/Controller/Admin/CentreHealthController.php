<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CentreHealthController extends AbstractController
{
    /**
     * @Route("/admin/centre/health", name="admin_centre_health")
     */
    public function index()
    {
        return $this->render('admin/centre_health/index.html.twig', [
            'controller_name' => 'CentreHealthController',
        ]);
    }
}
