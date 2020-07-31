<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{

    protected $userRepository;
    protected $entityManager;

    function __construct(UserRepository $userRepository ,EntityManagerInterface $entityManager)
    {
        $this->userRepository= $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/homepage", name="homepage")
     */
    public function index()
    {
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }

    /**
     * @Route("/check-users", name="check_users")
     */
    public function check_users()
    {
        if ($this->isGranted('ROLE_PATIENT')) {
            return $this->redirectToRoute('patient');
        }
        elseif ($this->isGranted('ROLE_PRATICIEN')){
            return $this->redirectToRoute('praticien');
        }
    }

    /**
     * @Route("/change-locale/{locale}", name="change_locale")
     */
    public  function ChangeLocale($locale, Request $request )
    {
        //on stocke la langue démandé dans la session
        $request->getSession()->set('_locale',$locale);

        // on revient sur la page précédente
        return $this->redirect($request->headers->get('referer'));

    }
    /**
     * @Route("/modification/photo", name="modif_photo")
     */

    public function ModifPhoto(Request $request)
    {
        $user = $this->getUser();
        $users= $this->userRepository->find($user);
        $image = $request->request->get('image');
        $data = $image;
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $imageName = time().'.png';
        file_put_contents('uploads/'.$imageName, $data);
        if(!empty($imageName)){
            $users->setPhoto($imageName);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($users);
            $entityManager->flush();
        }
        return new JsonResponse(array("data" => "OK"));

    }
}
