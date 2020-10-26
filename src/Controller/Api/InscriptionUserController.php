<?php

namespace App\Controller\Api;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Fonction;
use App\Entity\Patient;
use App\Entity\Praticien;
use App\Entity\State;
use App\Entity\TypePatient;
use App\Entity\User;
use App\Repository\CityRepository;
use App\Repository\StateRepository;
use App\Repository\TypePatientRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\IFTTTHandler;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\DateTime as ConstraintsDateTime;

class InscriptionUserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;
 
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $userPasswordEncoderInterface)
    {
        $this->em = $em;
        $this->encoder = $userPasswordEncoderInterface;
    }
     
    public function __invoke(User $data, Request $request, EntityManagerInterface $entityManager, TypePatientRepository $typePatientRepository, CityRepository $cityRepository, StateRepository $stateRepository)
    {
        $user = json_decode($request->getContent(), true);

        $email = $user["email"];
        $first_name = $user["first_name"];
        $username = $user["username"];
        $last_name = $user["last_name"];
        $roles = $user["roles"];
        $password = $user["password"];
        if(strcmp($roles, "ROLE_PATIENT") == 0) {
            $date_on_born = $user["date_on_born"];
            $type_patient = $typePatientRepository->find(intval($user["type_patient"]));
        }
        $data->setPassword($this->encoder->encodePassword($data,$password));
        $data->setEmail($email);
        $data->setEtat(1);
        $data->setFirstName($first_name);
        $data->setLastName($last_name);
        $data->setUsername($username);
        $data->setRoles([$roles]);
        $sexe = $user["sexe"];

        $addresse = $user["address"];
        $phone = $user["phone"];
        $city = $user["city"];
        $city = $cityRepository->find($city);
        $state = $user["state"];
        $state = $stateRepository->find($state);
        if(strcmp($roles, "ROLE_PRATICIEN") == 0){
             $this->add_praticient($entityManager, $data, $phone,new DateTime($user["date_born"]),$city,$state,$sexe, $user["fonction"],$addresse);
             $this->add_patient_praticient($entityManager, $data,$addresse, $sexe, new DateTime($user["date_born"]), $password,$phone);
        }
        if(strcmp($roles, "ROLE_PATIENT") == 0){
            $this->add_patient($entityManager, $data, $type_patient,$city,$state, $addresse, $sexe, new DateTime($date_on_born),$phone);

        }
        $this->addFlash('success', 'L\'utilisateur a été enregistré avec succès !');
    }

    public function add_patient_praticient(EntityManager $entityManager, User $user, string $address, string $sexe, DateTime $naissance, $password,string $phone){
        $userPatient = new User();
        $userPatient->setPassword($this->encoder->encodePassword($userPatient,$password));
        $userPatient->setEmail($user->getEmail());
        $userPatient->setEtat(1);
        $userPatient->setFirstName($user->getFirstName());
        $userPatient->setLastName($user->getLastName());
        $userPatient->setUsername($user->getUsername().rand(1, 20));
        $userPatient->setRoles(['ROLE_PATIENT']);
        $entityManager->persist($userPatient);
        $patient = new Patient();
        $patient->setUser($userPatient);
        $patient->setLastName($user->getLastName());
        $patient->setFirstName($user->getFirstName());
        $patient->setLastName($user->getLastName());
        $patient->setPhone($phone);
        $patient->setDateOnBorn($naissance);
        $patient->setAddress($address);
        $patient->setSexe($sexe);
        $entityManager->persist($patient);
        $entityManager->flush();

    }


    public function add_patient(EntityManager $entityManager, User $user, TypePatient $typePatient,City $city, State $state, string $address, string $sexe, DateTime $naissance,string $phone){
        $patient = new Patient();
        $patient->setUser($user);
        $patient->setTypePatient($typePatient);
        $patient->setLastName($user->getLastName());
        $patient->setFirstName($user->getFirstName());
        $patient->setLastName($user->getLastName());
        $patient->setPhone($phone);
        $patient->setCity($city);
        $patient->setState($state);
        $patient->setDateOnBorn($naissance);
        $patient->setAddress($address);
        $patient->setSexe($sexe);
        $entityManager->persist($patient);
        $entityManager->flush();

    }

    public function add_praticient(EntityManager $entityManager,User $user, string $phone, DateTime $naissance, City $city, State $state, string $sexe, string $fonction,string $address){
        $fonc = new Fonction();
        $fonc->setNomFonction($fonction);
        $entityManager->persist($fonc);
        $entityManager->flush();
        $praticien = new Praticien();
        $praticien->setUser($user);
        $praticien->setLastName($user->getLastName());
        $praticien->setFirstName($user->getFirstName());
        $praticien->setLastName($user->getLastName());
        $praticien->setPhone($phone);
        $praticien->setSexe($sexe);
        $praticien->setAddress($address);
        $praticien->setCountryFonction($state);
        $praticien->setFonction($fonc);
        $praticien->setCityFonction($city);
        $praticien->setDateBorn($naissance);
        $entityManager->persist($praticien);
        $entityManager->flush();

    }


}

