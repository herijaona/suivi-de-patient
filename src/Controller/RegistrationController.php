<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\Praticien;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RegistrationPraticienFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        if($this->checkConnected()){
            return $this->redirectToRoute('_login_redirect');
        }
        $user = [];
        $form = $this->createForm(RegistrationFormType::class, $user);
       
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $last_name = $form->get('lastname')->getData();
            $first_name = $form->get('firstname')->getData();
            $user = new User();
            $user->setLastName($last_name);
            $user->setFirstName($first_name);
            $user->setEmail($form->get('email')->getData());
            $user->setRoles(['ROLE_PATIENT']);
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $patient = new Patient();
            $patient->setFirstName($first_name);
            $patient->setLastName($last_name);
            $patient->setAdress($form->get('address')->getData());
            $patient->setSexe($form->get('sexe')->getData());
            $patient->setDateOnBorn($form->get('date_naissance')->getData());
            $patient->setAdressOnBorn($form->get('lieu_naissance')->getData());
            $patient->setTypePatient($form->get('type_patient')->getData());
            $patient->setPhone($form->get('phone')->getData());
            $patient->setFatherName($form->get('namedaddy')->getData());
            $patient->setMotherName($form->get('namemonther')->getData());
            $patient->setEtat(1);
            $patient->setUser($user);
            $entityManager->persist($patient);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été enregistré avec succès !');
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        } 

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/praticien", name="app_register_praticien")
     */
    public function register_praticien(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        if($this->checkConnected()){
            return $this->redirectToRoute('_login_redirect');
        }
        $user = [];
        $form = $this->createForm(RegistrationPraticienFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $last_name = $form->get('lastname')->getData();
            $first_name = $form->get('firstname')->getData();
            $user = new User();
            $user->setLastName($last_name);
            $user->setFirstName($first_name);
            $user->setEmail($form->get('email')->getData());
            $user->setRoles(['ROLE_PRATICIEN']);
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $praticien = new Praticien();
            $praticien->setFirstName($first_name);
            $praticien->setLastName($last_name);
            $praticien->setCreatedAt(new \DateTime('now'));
            $praticien->setAdress($form->get('address')->getData());
            $praticien->setDateBorn($form->get('date_naissance')->getData());
            $praticien->setAdressBorn($form->get('lieu_naissance')->getData());
            $praticien->setFonction($form->get('fonction')->getData());
            $praticien->setPhone($form->get('phone')->getData());
            $praticien->setPhoneProfessional($form->get('phone_professional')->getData());
            $praticien->setUser($user);
            $entityManager->persist($praticien);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été enregistré avec succès !');
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register_praticien.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    public function checkConnected(){
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('ROLE_PATIENT')) {
            return true;
        }elseif($securityContext->isGranted('ROLE_PRATICIEN')){
            return true;
        }elseif($securityContext->isGranted('ROLE_ADMIN')){
            return true;
        }
        return false;
    }
}
