<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/login_user", name="login_user")
     */
    public function login()
    {
        return $this->render('authentication/login.html.twig', [
            'controller_name' => 'AuthenticationController',
        ]);
    }

    /**
     * @Route("/register_user", name="register_user")
     */
    public function register()
    {
        return $this->render('authentication/register.html.twig', [
            'controller_name' => 'AuthenticationController',
        ]);
    }

}
