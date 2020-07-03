<?php

namespace App\Controller\Api;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Patient;
use App\Entity\Praticien;
use App\Entity\TypePatient;
use App\Entity\User;
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
     
    public function __invoke(User $data, Request $request)
    {
        $user = json_decode($request->getContent(), true);

        $email = $user["email"];
        $first_name = $user["first_name"];
        $username = $user["username"];
        $last_name = $user["last_name"];
        $roles = $user["roles"];
        $password = $user["password"];
        $date_on_born = $user["date_on_born"];

        $data->setPassword($this->encoder->encodePassword($data,$password));
        $data->setEmail($email);
        $data->setEtat(1);
        $data->setFirstName($first_name);
        $data->setLastName($last_name);
        $data->setUsername($username);
        $temp = $data->getRoles();
        if(strcmp($roles, "ROLE_PRATICIENT")==0){
            $temp[] = $roles;  
        }
        $data->setRoles($temp);
        $entityManager = $this->getDoctrine()->getManager();
        $sexe = $user["sexe"];
        $type_patient = $this->getDoctrine()->getRepository(TypePatient::class)->find(intval($user["type_patient"]));
        $this->add_patient($entityManager, $data, $type_patient, $sexe, new DateTime($date_on_born));
        if(strcmp($roles, "ROLE_PRATICIENT")==0){
             $addresse_praticient = $this->getDoctrine()->getRepository(Address::class)->find(intval($user["id_address"]));
             $this->add_praticient($entityManager, $data, $user["telephone"],new DateTime($date_on_born), $addresse_praticient, $user["fonction"]);
        }
        return $data;
    }

    public function add_patient(EntityManager $entityManager, User $user, TypePatient $typePatient, string $sexe, DateTime $naissance){
        $patient = new Patient();
        $patient->setUser($user);
        $patient->setTypePatient($typePatient);
        $patient->setLastName($user->getLastName());
        $patient->setFirstName($user->getFirstName());
        $patient->setLastName($user->getLastName());
        $patient->setDateOnBorn($naissance);
        $patient->setSexe($sexe);
        $entityManager->persist($patient);
        $entityManager->flush();
    }

    public function add_praticient(EntityManager $entityManager,User $user, string $numeroPhone, DateTime $naissance, Address $address, string $fonction){
        $praticient = new Praticien();
        $praticient->setUser($user);
        $praticient->setAddress($address);
        $praticient->setLastName($user->getLastName());
        $praticient->setFirstName($user->getFirstName());
        $praticient->setLastName($user->getLastName());
        $praticient->setPhone($numeroPhone);
        $praticient->setPhoneProfessional($numeroPhone);
        $praticient->setFonction($fonction);
        $praticient->setDateBorn($naissance);
        // dd($praticient->getFonction());
        $entityManager->persist($praticient);
        $entityManager->flush();
    }
}

