<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StateController extends AbstractController
{
    /**
     * @Route("/admin/state", name="admin_state")
     */
    public function index()
    {
        return $this->render('admin/state/index.html.twig', [
            'controller_name' => 'StateController',
        ]);
    }
}
