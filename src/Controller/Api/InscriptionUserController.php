<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class InscriptionUserController extends AbstractController
{
    protected $em;
 
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
     
    public function __invoke(User $data, Request $request)
    {
        $email = $request->query->get("email");
        $first_name = $request->query->get("first_name");
        $username = $request->query->get("username");
        $last_name = $request->query->get("last_name");
        $roles = $request->query->get("roles");
        $password = $request->query->get("password");
         dd($password,$email, $first_name, $username, $last_name, $roles);
        // return $data;
    }
}

