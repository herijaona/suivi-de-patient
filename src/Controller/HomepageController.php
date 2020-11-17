<?php

namespace App\Controller;

use App\Repository\CentreHealthRepository;
use App\Repository\OrdonnaceRepository;
use App\Repository\UserRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Flex\Response;

class HomepageController extends AbstractController
{

    protected $userRepository;
    protected $entityManager;
    protected $centreHealthRepository;
    protected $ordonnaceRepository;

    function __construct(UserRepository $userRepository,CentreHealthRepository $centreHealthRepository, OrdonnaceRepository $ordonnaceRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->centreHealthRepository= $centreHealthRepository;
        $this->ordonnaceRepository= $ordonnaceRepository;
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
        if ($this->isGranted('ROLE_PATIENT') ) {
            return $this->redirectToRoute('patient');
        } elseif ($this->isGranted('ROLE_PRATICIEN')) {
            return $this->redirectToRoute('praticien');
        }
    }

    /**
     * @Route("/change-locale/{locale}", name="change_locale")
     */
    public  function ChangeLocale($locale, Request $request)
    {
        //on stocke la langue démandé dans la session
        $request->getSession()->set('_locale', $locale);
        // on revient sur la page précédente
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/modification/photo", name="modif_photo")
     *
     */

    public function ModifPhoto(Request $request, FileUploadService $fileUploadService)
    {
        $user = $this->getUser();
        $users = $this->userRepository->find($user);
        $image = $request->files->get('images');
        if ($image != '') {
            $users->setPhoto($fileUploadService->upload($image, $this->getParameter('images_directory')));
            $this->entityManager->persist($users);
            $this->entityManager->flush();
        }
        return new JsonResponse(array("data" => "OK"));
    }

    /**
     * @Route("/update/password", name="update_password")
     */
     public function update_password(Request $request,MailerInterface $mailer,TranslatorInterface $translator)
     {
         $mail = $request->request->get('email');

         if ($mail != null){
             $user = $this->userRepository->findOneBy(['email'=>$mail]);
            if ($user == null){
                $message = $translator->trans('your e-mail does not correspond to any user');
                $this->addFlash('error', $message);

            }
            else{
                $email = (new TemplatedEmail())
                    ->from('digitalhealth@matipla.com')
                    ->to($mail)
                    ->subject('Confirmation code' )
                    ->htmlTemplate('email/change.html.twig');
                // On envoie le mail
                $mailer->send($email);
                $message=$translator->trans('The password reset link is sent to your email');
                $this->addFlash('success', $message.' '.$mail);


            }
         }

        return $this->render('security/email.html.twig');
     }


     /**
      * @Route("/reset/pass", name="reset_pass")
      */
     public function reset_pass(Request $request,UserPasswordEncoderInterface $userPasswordEncoder,TranslatorInterface $translator){
         $username = $request->request->get('username');
         $passworde= $request->request->get('password');
         if ($username!=null && $passworde != null){
             $user = $this->userRepository->findOneBy(['username'=>$username]);
             $password = $userPasswordEncoder->encodePassword($user, $passworde);
             $user->setPassword($password);
             $message=$translator->trans('Password reset success');
             $this->addFlash('success', $message);
             $this->entityManager->persist($user);
             $this->entityManager->flush();

             return $this->redirectToRoute('app_login');
         }


         return $this->render('security/_forget_password.html.twig');

     }






}
