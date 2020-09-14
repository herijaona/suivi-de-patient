<?php

namespace App\Controller\Profile;

use App\Entity\Ordonnace;
use App\Entity\Praticien;
use App\Entity\User;

use App\Entity\Patient;
use App\Form\edit;
use App\Form\RegistrationPraticienFormType;
use App\Repository\OrdonnaceRepository;
use App\Repository\PatientRepository;

use App\Repository\TypePatientRepository;
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

use App\Security\LoginFormAuthenticator;
use App\Repository\UserRepository;

class ProfileController extends AbstractController
{
    protected $user;
    protected $patientRepository;
    protected $praticienRepository;
    protected $ordonnaceRepository;
    protected $entityManager;
    protected $typePatientRepository;
    private $userCurrent;

    const ROLE_PATIENT = 'ROLE_PATIENT';
    const ROLE_PRATICIEN = 'ROLE_PRATICIEN';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    function __construct(
        PatientRepository $patientRepository,
        OrdonnaceRepository $ordonnaceRepository,
        PraticienRepository $praticienRepository,
        TokenStorageInterface $tokenStorage,
        TypePatientRepository $typePatientRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager

    )
    {
        $this->user = $userRepository;
        $this->typePatientRepository=$typePatientRepository;
        $this->ordonnaceRepository= $ordonnaceRepository;
        $this->patientRepository = $patientRepository;
        $this->praticienRepository = $praticienRepository;
        $this->userCurrent = $tokenStorage->getToken()->getUser();
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/form-edit/praticien", name="add_edit_praticien", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @return JsonResponse
     */
    public function form_edit_praticien(Request $request){
        $pra=[];
        $pra['id']= $request->request->get('id');
        $praticien = $this->praticienRepository->find($pra['id']);
        $pra['numero']= $praticien->getNumeroProfessionnel();
        $pra['address']=$praticien->getAddress();
        $ordonance= $this->ordonnaceRepository->findOneBy(['praticien'=>$praticien]);
        $pra['center_health']= $ordonance->getCentreSante();
        $form = $this->createForm(RegistrationPraticienFormType::class, $pra);
        $response = $this->renderView('profile/_form_edit_praticien.html.twig', [
            'form' => $form->createView(),
            'eventData' => $pra,
        ]);
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);
    }

    /**
     * @Route("/form-edit", name="add_edit", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @return JsonResponse
     */
    public function form_edit(Request $request){
        $pro= [];
        $pro['id'] = $request->request->get('id');

        $patient = $this->patientRepository->find($pro['id']);
        $pro['type_patient'] = $patient->getTypePatient();
        $pro['address']= $patient->getAddress();
        $pro['sexe']=$patient->getSexe();
        $pro['type_patient']=$patient->getTypePatient();
        $enceinte= $patient->getIsEnceinte();
        $form = $this->createForm(RegistrationFormType::class, $pro);
        $response = $this->renderView('profile/_form_edit.html.twig', [
            'form' => $form->createView(),
            'enceinte'=>$enceinte,
            'eventData' => $pro,
        ]);
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);
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
     * @Route("/praticien/pr", name="editPr")
     * @return Response
     */
    public function editPr(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, TranslatorInterface $translator) : Response{

        $user = [];

        $form = $this->createForm(RegistrationPraticienFormType::class, $user);
        $form->handleRequest($request);
        $centre = $form->get('center_health')->getData();
        $address =$form->get('address')->getData();
        $numero =  $form->get('numero')->getData();
        $user = $this->getUser();
        $praticien =  $this->praticienRepository->findOneBy(['user' => $user]);
        $praticien->setNumeroProfessionnel($numero);
        $praticien->setAddress($address);
        $this->entityManager->persist($praticien);
        $this->entityManager->flush();
        $ordo = $this->ordonnaceRepository->findOneBy(['praticien' => $praticien]);
        $ordo->setCentreSante($centre);
        $this->entityManager->persist($ordo);
        $this->entityManager->flush();

        return $this->redirectToRoute('editPraticien');

    }
    /**
     * @Route("/patient/pro", name="editP")
     * @return Response
     */
    public function editP(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, TranslatorInterface $translator) : Response{
        $pro= [];
        $form = $this->createForm(RegistrationFormType::class, $pro);
        $form->handleRequest($request);
        $address= $form->get('address')->getData();
        $enceinte = $request->request->get('liste');
        $date = new \DateTime();
        $type = $form->get('type_patient')->getData();
        $user= $this->getUser();
        $patient = $this->patientRepository->findOneBy(['user'=>$user]);
        if($enceinte != null ){
            if($enceinte == 1) {
                $patient->setIsenceinte($enceinte);
                $patient->setDateEnceinte($date);
            }elseif($enceinte == 0){
                $patient->setIsenceinte($enceinte);
            }
        }
        $patient->setTypePatient($type);
        $patient->setAddress($address);
        $this->entityManager->persist($patient);
        $this->entityManager->flush();

        return $this->redirectToRoute('editPatient');

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
