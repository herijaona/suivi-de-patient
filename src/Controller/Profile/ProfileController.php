<?php

namespace App\Controller\Profile;


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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class ProfileController extends AbstractController
{
    protected $patientRepository;
    protected $praticienRepository;
    private $userCurrent;
    function __construct(
        PatientRepository $patientRepository,
        PraticienRepository $praticienRepository,
        TokenStorageInterface $tokenStorage
    )
    {
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

}
