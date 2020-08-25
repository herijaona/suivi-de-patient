<?php

namespace App\Controller\Profile;

use App\Entity\Praticien;
use App\Entity\User;

use App\Entity\Patient;
use App\Repository\PatientRepository;

use Symfony\Component\Routing\Annotation\Route;

use App\Form\RegistrationFormType;
use App\Entity\Family;
use App\Entity\GroupFamily;
use App\Entity\RendezVous;
use App\Repository\FamilyRepository;
use App\Repository\GroupFamilyRepository;
use App\Repository\PraticienRepository;
use App\Repository\RendezVousRepository;
use App\Service\VaccinGenerate;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Form\RegistrationPraticienFormType;
use App\Security\LoginFormAuthenticator;
use App\Repository\UserRepository;

class ProfileController extends AbstractController
{
    protected $user;
    protected $patientRepository;
    protected $praticienRepository;
    private $userCurrent;

    const ROLE_PATIENT = 'ROLE_PATIENT';
    const ROLE_PRATICIEN = 'ROLE_PRATICIEN';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    function __construct(
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        TokenStorageInterface $tokenStorage,
        UserRepository $userRepository
    )
    {
        $this->user = $userRepository;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->userCurrent = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("profile/{id}-{slug}", name="profile")
     * @return Response
     */
    public function profile($slug, $id) : Response
    {   
    
        if ($this->isGranted('ROLE_PRATICIEN') || $this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('');
        }

        $idCurrent = $this->userCurrent;
        $user = $this->getUser();
        $isuser = $this->patientRepository->findByPatient(['user'=>$user]);

        return $this->render('profile/profilePatient.html.twig', [
            'isuser' => $isuser,
            'slug' => $slug,
            'id' => $id
        ]);
      
    }

    /**
     * @Route("praticienProfile/{id}-{slug}", name="praticienProfile")
     * @return Response
     */
    public function praticienProfile($slug, $id) : Response
    {   

        $user = $this->getUser();
        $isuser = $this->praticienRepository->findByPraticien(['user'=>$user]);
        return $this->render('profile/profilePraticien.html.twig', [
            'isuser' => $isuser,
            'slug' => $slug,
            'id' => $id
        ]);
      
    }

    /**
     * @Route("/praticien/profile", name="editPraticien")
     * @return Response
     */
    public function editPraticien(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator,TranslatorInterface $translator) : Response{
        $user = [];
        $form = $this->createForm(RegistrationPraticienFormType::class, $user);

        $form->handleRequest($request);

        $currentUser = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            dd("tonga");
            $last_name = $form->get('lastname')->getData();
            $first_name = $form->get('firstname')->getData();
         
            $user = $this->user->find($currentUser->getId());

            $user->setLastName($last_name);
            $user->setFirstName($first_name);
            $user->setEmail($form->get('email')->getData());
            $user->setRoles(['ROLE_PRATICIEN']);
            $user->setEtat(0);
            // encode the plain password
            $user->setPassword( 
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);

            $idPraticien = $this->praticienRepository->findByPraticienId($currentUser->getId());
            $praticien = $this->praticienRepository->find($idPraticien['0']['id']);

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
            $message = $translator->trans('The user has been registered successfully!');
            $this->addFlash('success', $message);
            return $this->redirectToRoute('editPraticien',['id'=>$idPraticien['0']['id']]);
        }

        $isuser = $this->praticienRepository->findByPraticien(['user'=>$user]);

        $coprs = $this->praticienRepository->findByPraticienUser($currentUser->getId());
        
        $values="";
        foreach ( $coprs as $key => $val) { 
            $values = $val; 
        }

        return $this->render('profile/editPraticien.html.twig', [
            'values' => $values,
            'isuser' => $isuser,
            'registrationForm' => $form->createView(),
            'currentUser' => $currentUser
        ]);
    }

    
    /**
     * @Route("/patient/profile", name="editPatient")
     * @return Response
     */
    public function editPatient(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, TranslatorInterface $translator) : Response{

        $user = [];
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $currentUser = $this->getUser();
        $user = $this->user->find($currentUser->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $last_name = $form->get('lastname')->getData();
            $first_name = $form->get('firstname')->getData();
            $user->setLastName($last_name);
            $user->setFirstName($first_name);
            $user->setUsername($form->get('username')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setRoles([self::ROLE_PATIENT]);
            $user->setEtat(0);
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $idPatient = $this->patientRepository->findByPatientId($currentUser->getId());

            $patient = $this->patientRepository->find($idPatient['0']['id']);
            $patient->setFirstName($first_name);
            $patient->setLastName($last_name);

            $patient->setAddress($form->get('address')->getData());
            $patient->setSexe($form->get('sexe')->getData());
            $patient->setDateOnBorn($form->get('date_naissance')->getData());
            $patient->setAddressOnBorn($form->get('lieu_naissance')->getData());

            $patient->setTypePatient($form->get('type_patient')->getData());
            $patient->setPhone($form->get('phone')->getData());
            $patient->setFatherName($form->get('namedaddy')->getData());
            $patient->setMotherName($form->get('namemonther')->getData());
            $patient->setEtat(1);
            $patient->setUser($user);
            $entityManager->persist($patient);
            $entityManager->flush();
            $message = $translator->trans('The user has been registered successfully!');
            $this->addFlash('success', $message);
            // do anything else you need here, like send an email

            return $this->redirectToRoute('editPatient',['id'=>$currentUser->getId()]);
        }


        $isuser = $this->patientRepository->findByPatient(['user'=>$user]);

        $coprs = $this->patientRepository->findByPatientUser($currentUser->getId());
        
        $values = "";
        foreach ( $coprs as $key => $val) { 
            $values = $val;
        }
        
        return $this->render('profile/profilePatient.html.twig', [
            'values' => $values,
            'isuser' => $isuser,
            'registrationForm' => $form->createView(),
            'currentUser' => $currentUser
        ]);

    }

    public function checkConnected(){
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted(self::ROLE_PATIENT)) {
            return 'patient';
        }elseif($securityContext->isGranted(self::ROLE_PRATICIEN)){
            return 'praticien';
        }elseif($securityContext->isGranted(self::ROLE_ADMIN)){
            return 'admin';
        }
        return false;
    }

}
