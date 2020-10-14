<?php

namespace App\Controller\Profile;

use App\Form\RegistrationPraticienFormType;
use App\Repository\CityRepository;

use App\Repository\FonctionRepository;
use App\Repository\OrdonnaceRepository;
use App\Repository\PatientRepository;

use App\Repository\StateRepository;
use App\Repository\TypePatientRepository;
use Exception;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationFormType;
use App\Repository\PraticienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Contracts\Translation\TranslatorInterface;
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
    protected $stateRepository;
    protected $cityRepository;
    protected $fonctionRepository;

    private $userCurrent;

    const ROLE_PATIENT = 'ROLE_PATIENT';
    const ROLE_PRATICIEN = 'ROLE_PRATICIEN';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    function __construct(
        PatientRepository $patientRepository,
        OrdonnaceRepository $ordonnaceRepository,
        PraticienRepository $praticienRepository,
        FonctionRepository $fonctionRepository,
        StateRepository $stateRepository,
        CityRepository $cityRepository,
        TokenStorageInterface $tokenStorage,
        TypePatientRepository $typePatientRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager

    )
    {
        $this->user = $userRepository;
        $this->stateRepository=$stateRepository;
        $this->fonctionRepository=$fonctionRepository;
        $this->cityRepository=$cityRepository;
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
        $numero = $praticien->getNumeroProfessionnel();
        $pra['address']=$praticien->getAddress();
        $pra['lastname']= $praticien->getLastName();
        $pra['firstname']= $praticien->getFirstName();
        $pra['date_naissance']= $praticien->getDateBorn()->format('Y-m-d H:i:s');
        $pra['phone']= $praticien->getPhone();
        $pra['sexe']= $praticien->getSexe();
        $pra['CountryOnBorn']= $praticien->getCountryOnBorn();
        $pra['username']= $praticien->getUser()->getUsername();

        $pra['email']=$praticien->getUser()->getEmail();
        $pra['plainPassword']= $praticien->getUser()->getPassword();
        $phone = $praticien->getPhone();

        $ordonance= $this->ordonnaceRepository->findOneBy(['praticien'=>$praticien]);

        $pra['center_health']= $ordonance->getCentreSante();
        $fonction = $this->fonctionRepository->findOneBy(['Praticien'=>$praticien]);
        $pra['country']= $fonction->getState();
        $fonc = $fonction->getFonction();
        $city = $fonction->getCity();
        $cityborn = $praticien->getCityOnBorn();
        $state = $fonction->getState();

        $form = $this->createForm(RegistrationPraticienFormType::class, $pra);
        $response = $this->renderView('profile/_form_edit_praticien.html.twig', [
            'form' => $form->createView(),
            'phone'=>$phone,
            'fonction'=>$fonc,
            'cityBorn'=>$cityborn,
            'city'=>$city,
            'state'=>$state,
            'numero'=>$numero,
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
        $pro['namedaddy']=$patient->getFatherName();
        $pro['date_naissance']= $patient->getDateOnBorn()->format('Y-m-d H:i:s');
        $pro['phone']= $patient->getPhone();
        $pro['username']= $patient->getUser()->getUsername();
        $pro['plainPassword']= $patient->getUser()->getPassword();
        $pro['firstname']= $patient->getFirstName();
        $pro['lastname']= $patient->getLastName();
        $pro['namemonther']=$patient->getMotherName();
        $pro['type_patient']=$patient->getTypePatient();
        $pro['email']=$patient->getUser()->getEmail();
        $pro['country']=$patient->getState();
        $pro['CountryOnBorn']=$patient->getCountryOnborn();
        $city = $patient->getCity();
        $cityborn = $patient->getCityOnBorn();
        $phone= $patient->getPhone();
        $enceinte= $patient->getIsEnceinte();

        $form = $this->createForm(RegistrationFormType::class, $pro);
        $response = $this->renderView('profile/_form_edit.html.twig', [
            'form' => $form->createView(),
            'enceinte'=>$enceinte,
            'cityBorn'=>$cityborn,
            'phone'=>$phone,
            'city'=>$city,
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
        $ordonance= $this->ordonnaceRepository->findOneBy(['praticien'=>$coprs]);
        $centre = $ordonance->getCentreSante();
        $fonction = $this->fonctionRepository->findOneBy(['Praticien'=>$coprs]);
        $fonc = $fonction->getFonction();
        $city = $fonction->getCity();
        $state = $fonction->getState();
        if($centre != null){
            $centre=$ordonance->getCentreSante()->getCentreName();
        }
        $values="";
        foreach ( $coprs as $key => $val) {
            $values = $val;
        }

        return $this->render('profile/editPraticien.html.twig', [
            'values' => $values,
            'isuser' => $isuser,
            'fonction'=>$fonc,
            'city'=>$city,
            'state'=>$state,
            'centre'=>$centre,
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
        $countryborn = $form->get('CountryOnBorn')->getData();
        $countryborn = $this->stateRepository->find($countryborn);
        $fonc = $request->request->get('fonction');

        $address =$form->get('address')->getData();
        $mail= $form->get('email')->getData();
        $cityborn= $request->request->get('cityborn');
        $cityborn= $this->cityRepository->find($cityborn);
        $city= $request->request->get('city');
        $city= $this->cityRepository->find($city);
        $numero =  $request->request->get('numero');
        $user = $this->getUser();
        $praticien =  $this->praticienRepository->findOneBy(['user' => $user]);
        $praticien->setNumeroProfessionnel($numero);
        $praticien->setCityOnBorn($cityborn);
        $praticien->setCountryOnBorn($countryborn);

        $praticien->setAddress($address);
        $this->entityManager->persist($praticien);
        $this->entityManager->flush();
        $ordo = $this->ordonnaceRepository->findOneBy(['praticien' => $praticien]);
        $ordo->setCentreSante($centre);
        $this->entityManager->persist($ordo);
        $this->entityManager->flush();
        $fonction = $this->fonctionRepository->findOneBy(['Praticien'=>$praticien]);
        $fonction->setFonction($fonc);
        $fonction->setCity($city);
        $country = $form->get('country')->getData();
        $country = $this->stateRepository->find($country);
        $fonction->setState($country);
        $this->entityManager->persist($fonction);
        $this->entityManager->flush();
        $user= $this->user->find($user);
        $user->setEmail($mail);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('editPraticien');

    }

    /**
     * @Route("/patient/pro", name="editP")
     * @return Response
     * @throws Exception
     */
    public function editP(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, TranslatorInterface $translator) : Response{
        $pro= [];
        $form = $this->createForm(RegistrationFormType::class, $pro);
        $form->handleRequest($request);
        $address= $form->get('address')->getData();
        $mail= $form->get('email')->getData();
        $state= $form->get('country')->getData();
        $state = $this->stateRepository->find($state);
        $countryborn = $form->get('CountryOnBorn')->getData();
        $countryborn = $this->stateRepository->find($countryborn);
        $city= $request->request->get('city');
        $city= $this->cityRepository->find($city);
        $cityborn= $request->request->get('cityborn');
        $cityborn= $this->cityRepository->find($cityborn);
        $enceinte = $request->request->get('liste');
        $phone = $request->request->get('phone');
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
        $patient->setCountryOnborn($countryborn);
        $patient->setCityOnBorn($cityborn);
        $patient->setState($state);
        $patient->setCity($city);
        $patient->setPhone($phone);
        $this->entityManager->persist($patient);
        $this->entityManager->flush();
        $user = $this->user->find($user);
        $user->setEmail($mail);
        $this->entityManager->persist($user);
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