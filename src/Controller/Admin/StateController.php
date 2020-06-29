<?php

namespace App\Controller\Admin;

use App\Entity\State;
use App\Form\CenterHealthType;
use App\Form\StateType;
use App\Repository\StateRepository;
use App\Repository\TypeVaccinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StateController extends AbstractController
{

    protected $stateRepository;
    protected $entityManager;

    function __construct(
        StateRepository $stateRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->stateRepository = $stateRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/state", name="admin_state")
     */
    public function index()
    {
        $state = $this->stateRepository->findAll();
        return $this->render('admin/state/index.html.twig', [
            'states' => $state,
        ]);
    }

    /**
     * @Route("/admin/state/add", name="add_form_state", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function add_form_state(Request $request)
    {
        $action = $request->request->get('action');
        $idState = $request->request->get('id_state');
        $eventData = [];
        //$form = $this->createForm(StateType::class, $eventData);
        if ($action == "new") {
            $state = new State();
            $form = $this->createForm(StateType::class, $state);
            $response = $this->renderView('admin/state/new_form_state.html.twig', [
                'new' => true,
                'form' => $form->createView(),
                'eventData' => $eventData,
            ]);
        } else {
            $State = $this->stateRepository->find($idState);
            $form = $this->createForm(StateType::class, $State);
            $response = $this->renderView('admin/state/new_form_state.html.twig', [
                'new' => false,
                'form' => $form->createView(),
                'eventData' => $eventData,
            ]);
        }
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);
    }


    /**
     * @Route("/state/register", name="register_state", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function register_state(Request $request)
    {
        $stateRequest = $request->request->get('state');
        $nameState = $stateRequest['nameState'];
        if ($stateRequest['id'] != '' && $stateRequest['id'] != null){
            $State = $this->stateRepository->find($stateRequest['id']);
            $State->setNameState($nameState);
            $this->entityManager->persist($State);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le nom de pays à été modifié avec succès !');
        }else{
            $StateExist = $this->stateRepository->findOneBy(['nameState' => $nameState]);
            if($StateExist){
                $this->addFlash('warning', 'Le nom de pays à été déjà enregistré !');
            }else{
                $stateNew = new State();
                $stateNew->setNameState($nameState);
                $this->entityManager->persist($stateNew);
                $this->entityManager->flush();
                $this->addFlash('success', 'Le nom de pays à été enregistré avec succès !');
            }
        }
        return $this->redirectToRoute("admin_state");
    }

    /**
     * @Route("/admin/state/remove", name="remove_state", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_state(Request $request)
    {
        $idState = $request->request->get('id_state');
        $delete = false;
        if ($idState != '' && $idState != null){
            $State = $this->stateRepository->find($idState);
            if (null !=  $State ){
                $Region = $State->getRegions();
                if ($Region  && count($Region) > 0){
                    $delete = false;
                    $this->addFlash('error', 'Erreur de suprimé de cet élément !');
                }else{
                    $this->entityManager->remove($State);
                    $this->entityManager->flush();
                    $delete = true;
                    $this->addFlash('success', 'ville à été supprimé avec succès !');
                }
            }
        }
        return new JsonResponse(['form_delete' => $delete]);
    }
}
