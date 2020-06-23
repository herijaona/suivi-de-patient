<?php

namespace App\Controller\Profile;

use App\Entity\Praticien;
use App\Entity\User;

use App\Entity\Patient;
use App\Repository\PatientRepository;

use Symfony\Component\Routing\Annotation\Route;

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
        // if ($this->isGranted('ROLE_PATIENT') || $this->isGranted('ROLE_ADMIN')) {
        //     return $this->redirectToRoute('');
        // }
        
        // if($slug == 'patient'){
        //     $isuser = $this->patientRepository->find($id);
        // }else if($slug == 'praticien'){
        // $user = $this->getUser();
        // $isuser = $this->praticienRepository->findByPraticien(['user'=>$user]);
        // }

        $user = $this->getUser();
        $isuser = $this->praticienRepository->findByPraticien(['user'=>$user]);


        return $this->render('profile/profilePraticien.html.twig', [
            'isuser' => $isuser,
            'slug' => $slug,
            'id' => $id
        ]);
      
    }

    /**
     * @Route("praticien/profile", name="editPraticien")
     * @return Response
     */
    public function editPraticien(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator) : Response{
        $user = [];
        $form = $this->createForm(RegistrationPraticienFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $last_name = $form->get('lastname')->getData();
            $first_name = $form->get('firstname')->getData();
         
            $currentUser = $this->getUser();
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
            $this->addFlash('success', 'L\'utilisateur a été enregistré avec succès !');

            return $this->redirectToRoute('editPraticien',['id'=>$idPraticien['0']['id']]);
        }
        return $this->render('profile/editPraticien.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }

}
