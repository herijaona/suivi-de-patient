<?php

namespace App\Controller\Api;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Patient;
use App\Entity\Praticien;
use App\Entity\TypePatient;
use App\Entity\User;
use App\Repository\CityRepository;
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
     
    public function __invoke(User $data, Request $request, EntityManagerInterface $entityManager, TypePatientRepository $typePatientRepository, CityRepository $cityRepository)
    {
        $user = json_decode($request->getContent(), true);

        $email = $user["email"];
        $first_name = $user["first_name"];
        $username = $user["username"];
        $last_name = $user["last_name"];
        $roles = $user["roles"];
        $password = $user["password"];
        $date_on_born = $user["date_on_born"];
        $num_rue = $user["num_rue"];
        $quartier = $user["quartier"];

        $data->setPassword($this->encoder->encodePassword($data,$password));
        $data->setEmail($email);
        $data->setEtat(1);
        $data->setFirstName($first_name);
        $data->setLastName($last_name);
        $data->setUsername($username);
        $data->setRoles([$roles]);
        $sexe = $user["sexe"];
        $type_patient = $typePatientRepository->find(intval($user["type_patient"]));
        $addresse = $cityRepository->find(intval($user["id_address"]));

        if(strcmp($roles, "ROLE_PRATICIENT") == 0){
             $this->add_praticient($entityManager, $data, $user["telephone"],new DateTime($date_on_born), $addresse, $user["fonction"], $num_rue, $quartier);
            $this->add_patient_praticient($entityManager, $data, $type_patient, $addresse, $sexe, new DateTime($date_on_born), $num_rue, $quartier, $password);
        }
        if(strcmp($roles, "ROLE_PATIENT") == 0){
            $this->add_patient($entityManager, $data, $type_patient, $addresse, $sexe, new DateTime($date_on_born), $num_rue, $quartier);
        }
        return $data;
    }

    public function add_patient_praticient(EntityManager $entityManager, User $user, TypePatient $typePatient, City $address, string $sexe, DateTime $naissance, $num_rue, $quartier, $password){
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
        $patient->setTypePatient($typePatient);
        $patient->setLastName($user->getLastName());
        $patient->setFirstName($user->getFirstName());
        $patient->setLastName($user->getLastName());
        $patient->setDateOnBorn($naissance);
        $patient->setCity($address);
        $patient->setNumRue($num_rue);
        $patient->setQuartier($quartier);
        $patient->setSexe($sexe);
        $entityManager->persist($patient);
        $entityManager->flush();
    }

    public function add_patient(EntityManager $entityManager, User $user, TypePatient $typePatient, City $address, string $sexe, DateTime $naissance, $num_rue, $quartier){
        $patient = new Patient();
        $patient->setUser($user);
        $patient->setTypePatient($typePatient);
        $patient->setLastName($user->getLastName());
        $patient->setFirstName($user->getFirstName());
        $patient->setLastName($user->getLastName());
        $patient->setDateOnBorn($naissance);
        $patient->setCity($address);
        $patient->setNumRue($num_rue);
        $patient->setQuartier($quartier);
        $patient->setSexe($sexe);
        $entityManager->persist($patient);
        $entityManager->flush();
    }

    public function add_praticient(EntityManager $entityManager,User $user, string $numeroPhone, DateTime $naissance, City $address, string $fonction, $num_rue, $quartier){
        $praticient = new Praticien();
        $praticient->setUser($user);
        $praticient->setCity($address);
        $praticient->setNumRue($num_rue);
        $praticient->setQuartier($quartier);
        $praticient->setLastName($user->getLastName());
        $praticient->setFirstName($user->getFirstName());
        $praticient->setLastName($user->getLastName());
        $praticient->setPhone($numeroPhone);
        $praticient->setPhoneProfessional($numeroPhone);
        $praticient->setFonction($fonction);
        $praticient->setDateBorn($naissance);
        $entityManager->persist($praticient);
        $entityManager->flush();
    }
}

