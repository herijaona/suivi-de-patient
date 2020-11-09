<?php

namespace App\Controller;

use App\Entity\Fonction;
use App\Entity\Ordonnace;
use App\Entity\Patient;
use App\Entity\Praticien;
use App\Entity\TypePatient;
use App\Entity\User;
use App\Form\ActivatorFormType;
use App\Form\RegistrationFormType;
use App\Form\RegistrationPraticienFormType;
use App\Repository\CityRepository;
use App\Repository\FonctionRepository;
use App\Repository\PatientRepository;
use App\Repository\PraticienRepository;
use App\Repository\StateRepository;
use App\Repository\TypePatientRepository;
use App\Repository\UserRepository;
use App\Repository\VaccinRepository;
use App\Security\LoginFormAuthenticator;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Service\VaccinGenerate;


class RegistrationController extends AbstractController
{

    protected $typePatientRepository;
    protected $userRepository;
    protected $cityRepository;
    protected $stateRepository;
    protected $vaccinGenerate;
    protected $vaccinRepository;


    const ROLE_PATIENT = 'ROLE_PATIENT';
    const ROLE_PRATICIEN = 'ROLE_PRATICIEN';
    const ROLE_ADMIN = 'ROLE_ADMIN';


    function __construct(UserRepository $userRepository,VaccinGenerate $vaccinGenerate, TypePatientRepository $typePatientRepository, CityRepository $cityRepository, StateRepository $stateRepository,VaccinRepository $vaccinRepository)
    {
        $this->vaccinGenerate = $vaccinGenerate;
        $this->userRepository = $userRepository;
        $this->typePatientRepository = $typePatientRepository;
        $this->cityRepository= $cityRepository;
        $this->stateRepository=$stateRepository;
        $this->vaccinRepository = $vaccinRepository;

    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @param MailerInterface $mailer
     * @return RedirectResponse|Response
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, MailerInterface $mailer)
    {
        if($this->checkConnected()){
            return $this->redirectToRoute($this->checkConnected());
        }
        $user = [];
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $code = $this->generate_code();

            // encode the plain password
            $last_name = $form->get('lastname')->getData();
            $date= $form->get('date_naissance')->getData();
            $date= DateTime::CreateFromFormat("d/m/Y", $date);
            $first_name = $form->get('firstname')->getData();
            $adresse= $form->get('address')->getData();
            $city = $request->request->get('city');
            $city =$this->cityRepository->find($city);
            $username = $form->get('username')->getData();
            $type_patient = $form->get('type_patient')->getData();
            $user = new User();
            $user->setLastName($last_name);
            $user->setFirstName($first_name);
            $user->setUsername($username);
            $user->setEmail($form->get('email')->getData());
            $user->setRoles([self::ROLE_PATIENT]);
            $user->setActivatorId($code);
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
            $patientRequest = $form->get('enceinte')->getData();

            $patient = new Patient();
            if($patientRequest == "true"){
                $etat = true;
                $dateenceinte = new DateTime();
                $patient->setDateEnceinte($dateenceinte);
            }else{
                $etat = false;
            }
            $patient->setFirstName($first_name);
            $patient->setLastName($last_name);
            $patient->setAddress($adresse);
            $patient->setSexe($form->get('sexe')->getData());
            $patient->setDateOnBorn($date);
            $patient->setCity($city);
            $patient->setTypePatient($type_patient);
            $patient->setState($form->get('country')->getData());
            $patient->setPhone($form->get('phone')->getData());
            $patient->setIsEnceinte($etat);
            $patient->setFatherName($form->get('namedaddy')->getData());
            $patient->setMotherName($form->get('namemonther')->getData());
            $patient->setEtat(1);
            $patient->setUser($user);
            $entityManager->persist($patient);
            $entityManager->flush();
            //  mail
            $email = (new TemplatedEmail())
                ->from('hello@neitic.com')
                ->to($form->get('email')->getData())
                ->subject('Confirmation code' )
                ->htmlTemplate('email/email.html.twig')
                ->context([
                    'code' => $code, 'name'=>$last_name,'first'=>$first_name, 'username'=>$username,'id'=>$user->getId()
                ]);
            // On envoie le mail
            $mailer->send($email);

            return $this->redirectToRoute('app_register_activate',['id'=>$user->getId()]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/praticien", name="app_register_praticien")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @param MailerInterface $mailer
     * @param FonctionRepository $fonctionRepository
     * @return RedirectResponse|Response
     * @throws TransportExceptionInterface
     */
    public function register_praticien(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, MailerInterface $mailer,FonctionRepository $fonctionRepository)
    {
        if($this->checkConnected()){
            return $this->redirectToRoute($this->checkConnected());
        }
        $user = [];
        $form = $this->createForm(RegistrationPraticienFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // CREATE ACCOUNT PRATICIEN
            $code = $this->generate_code();
            $fonc= $form->get('fonction')->getData();
            $fonction = $fonctionRepository->find($fonc);
            $username = $form->get('username')->getData();
            $last_name = $form->get('lastname')->getData();
            $first_name = $form->get('firstname')->getData();
            $email = $form->get('email')->getData();
            $city = $request->request->get('city');
            $city =$this->cityRepository->find($city);
            $country = $form->get('country')->getData();
            $country = $this->stateRepository->find($country);
            $date = $form->get('date_naissance')->getData();
            $date= DateTime::CreateFromFormat("d/m/Y", $date);
            $user = new User();
            $user->setLastName($last_name);
            $user->setFirstName($first_name);
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setRoles([self::ROLE_PRATICIEN]);
            $user->setActivatorId($code);
            $user->setEtat(false);
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
            $praticien->setFonction($fonction);
            $praticien->setLastName($last_name);
            $praticien->setSexe($form->get('sexe')->getData());
            $praticien->setCreatedAt(new \DateTime('now'));
            $praticien->setDateBorn($date);
            $praticien->setAddress($form->get('address')->getData());
            $praticien->setPhone($form->get('phone')->getData());
            $praticien->setEtat(false);
            $praticien->setUser($user);
            $praticien->setCountryFonction($country);
            $praticien->setCityFonction($city);
            $entityManager->persist($praticien);
            $entityManager->flush();
            $ordonance = new Ordonnace();
            $ordonance->setPraticien($praticien);
            $ordonance->setDatePrescription(new \DateTime('now'));
            $ordonance->setMedecinTraitant($praticien);
            $entityManager->persist($ordonance);
            $entityManager->flush();
            // CREATE ACCOUNT PATIENT
            $code2 = $this->generate_code();
            $last_name = $form->get('lastname')->getData();
            $first_name = $form->get('firstname')->getData();
            $userName2 = $this->random_username($last_name );
            $user2 = new User();
            $user2->setLastName($last_name);
            $user2->setFirstName($first_name);
            $user2->setRoles([self::ROLE_PATIENT]);
            $user2->setEmail($email);
            $user2->setUsername($form->get('username')->getData().$userName2);
            $user2->setActivatorId($code2);
            $user2->setEtat(false);
            // encode the plain password
            $user2->setPassword(
                $passwordEncoder->encodePassword(
                    $user2,
                    $form->get('plainPassword')->getData()
                )
            );
            $typePatient = $this->typePatientRepository->find(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user2);
            $patient = new Patient();
            $patient->setFirstName($first_name);
            $patient->setLastName($last_name);
            $patient->setSexe($form->get('sexe')->getData());
            $patient->setDateOnBorn($date);
            $patient->setState($form->get('country')->getData());
            $patient->setPhone($form->get('phone')->getData());
            $patient->setAddress($form->get('address')->getData());
            $patient->setTypePatient($typePatient);
            $patient->setCity($city);
            $patient->setEtat(false);
            $patient->setUser($user2);
            $entityManager->persist($patient);
            $entityManager->flush();
            $email = (new TemplatedEmail())
                ->from('hello@neitic.com')
                ->to($email)
                ->subject('Confirmation code' )
                ->htmlTemplate('email/email.html.twig')
                ->context([
                    'code' => $code, 'name'=>$last_name,'first'=>$first_name,  'username'=>$username,'id'=>$user->getId()
                ]);
            // On envoie le mail
            $mailer->send($email);
            return $this->redirectToRoute('app_register_activate',['id'=>$user->getId()]);
        }

        return $this->render('registration/register_praticien.html.twig', [
            'registrationForm' => $form->createView(),
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

    private function generate_code($length = 6) {
        $dico[0] = Array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        $dico[1] = Array(1,2,3,4,5,6,7,8,9,0);
        $text = "";
        for($i = 0; $i<$length; $i++) {
            $option = mt_rand(0, 1);
            $case = mt_rand(0,count($dico[$option])-1);
            $text .= $dico[$option][$case];
        }
        $user = $this->userRepository->findBy(['activatorId'=>$text]);
        if($user) {
            return $this->generate_code();
        }
        return $text;
    }

    private function random_username($string) {
        $pattern = " ";
        $firstPart = strstr(strtolower($string), $pattern, true);
        $secondPart = substr(strstr(strtolower($string), $pattern, false), 0,3);
        $nrRand = rand(0, 100);

        $username = trim($firstPart).trim($secondPart).trim($nrRand);
        // $username;
        $user = $this->userRepository->findBy(['username'=>$username]);
        if($user) {
            return $this->random_username($string);
        }
        return $username;
    }

    /**
     * @Route("/register/activate", name="app_register_activate")
     */
    public function activeCompte(Request $request, UserRepository $userRepository, PatientRepository $patientRepository, PraticienRepository $praticienRepository)
    {
        if($this->checkConnected()){
            return $this->redirectToRoute($this->checkConnected());
        }

        $form = $this->createForm(ActivatorFormType::class, []);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $add_code = $form->get('code')->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $auser = $userRepository->findOneBy(['activatorId' => $add_code]);
            if($auser){
                $patientUser = $patientRepository->findOneBy(['user' => $auser]);
                $praticienUser = $praticienRepository->findOneBy(['user' => $auser]);
                if($auser->getEtat() != 1){
                    $auser->setEtat(true);
                    $entityManager->persist($auser);
                    $entityManager->flush();
                }
                if ($patientUser && $patientUser->getEtat() != 1 ){
                    $patientUser->setEtat(true);

                    $entityManager->persist($patientUser);
                    $entityManager->flush();
                }

                if ($praticienUser && $praticienUser->getEtat() != 1 ){
                    $praticienUser->setEtat(true);
                    $entityManager->persist($praticienUser);
                    $entityManager->flush();
                }
                $this->addFlash('success', 'L\'utilisateur a été enregistré avec succès !');
                return $this->redirectToRoute('app_login');
            }else{
                $this->addFlash('error', 'Code non valide');
            }
        }
        $user = $userRepository->find($request->get('id'));
        $code =null;
        if($user){
            $code = $user->getActivatorId();
        }
        return $this->render('authentication/activator.html.twig', [
            'code' => $code,
            'activationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/country", name="country")
     */
    public function country(Request $request){
        $id = $request->request->get('id');
        $country = $this->stateRepository->find($id);
        $city = $this->cityRepository->searchCity($country);
        return new JsonResponse($city);
    }

    /**
     * @Route("/num", name="num")
     */
    public function num(Request $request){
        $id = $request->request->get('id');
        $country = $this->stateRepository->find($id);
        $num = $this->stateRepository->searchnum($country);
        return new JsonResponse($num);
    }


}
