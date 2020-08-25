<?php

namespace App\Controller\Admin;

use App\Entity\Region;
use App\Entity\State;
use App\Form\RegionType;
use App\Repository\RegionRepository;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegionController extends AbstractController
{

    protected $regionRepository;
    protected $entityManager;
    protected $stateRepository;

    function __construct(
        RegionRepository $regionRepository,
        StateRepository $stateRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->regionRepository = $regionRepository;
        $this->entityManager = $entityManager;
        $this->stateRepository = $stateRepository;
    }

    /**
     * @Route("/admin/region", name="admin_region")
     */
    public function index()
    {
        $regions = $this->regionRepository->findAll();
        return $this->render('admin/region/index.html.twig', [
            'regions' => $regions,
        ]);
    }

    /**
     * @Route("/admin/region/add", name="add_form_region", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function add_form_region(Request $request)
    {
        $action = $request->request->get('action');

        $idRegion = $request->request->get('id_region');
        $eventData = [];
        if ($action == "new") {
            $region = new Region();
            $form = $this->createForm(RegionType::class, $region);
            $response = $this->renderView('admin/region/new_form_region.html.twig', [
                'new' => true,
                'form' => $form->createView(),
                'eventData' => $eventData,
            ]);
        } else {
            $Region = $this->regionRepository->find($idRegion);
            $form = $this->createForm(RegionType::class, $Region);
            $response = $this->renderView('admin/region/new_form_region.html.twig', [
                'new' => false,
                'form' => $form->createView(),
                'eventData' => $eventData,
            ]);
        }
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);
    }


    /**
     * @Route("/region/register", name="register_region", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function register_region(Request $request, TranslatorInterface $translator )
    {
        $regionRequest = $request->request->get('region');
        $nameRegion = $regionRequest['nameRegion'];
        $idState = $regionRequest['state'];
        $State = $this->stateRepository->find($idState);
        if ($regionRequest['id'] != '' && $regionRequest['id'] != null){
            $Region = $this->regionRepository->find($regionRequest['id']);
            $Region->setNameRegion($nameRegion);
            $Region->setState($State);
            $this->entityManager->persist($Region);
            $this->entityManager->flush();
            $message = $translator->trans('modification successfully!');
            $this->addFlash('success', $message);
        }else{
            $RegionExist = $this->regionRepository->findOneBy(['nameRegion' => $nameRegion, 'state' => $State]);
            if($RegionExist){
                $this->addFlash('warning', 'Le nom de région à été déjà enregistré  !');
            }else{
                $regionNew = new Region();
                $regionNew->setNameRegion($nameRegion);
                $regionNew->setState($State);
                $this->entityManager->persist($regionNew);
                $this->entityManager->flush();
                $message = $translator->trans('The region name has been registered successfully!');
                $this->addFlash('success', $message);
            }
        }
        return $this->redirectToRoute("admin_region");
    }

    /**
     * @Route("/admin/region/remove", name="remove_region", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_region(Request $request, TranslatorInterface $translator)
    {
        $idRegion = $request->request->get('id_region');
        $delete = false;
        if ($idRegion != '' && $idRegion != null){
            $Region = $this->regionRepository->find($idRegion);
            if (null !=  $Region ){
                $Cities = $Region->getCities();
                if ($Cities  && count($Cities) > 0){
                    $delete = false;
                    $this->addFlash('error', 'Erreur de suprimé de cet élément !');
                }else{
                    $this->entityManager->remove($Region);
                    $this->entityManager->flush();
                    $message = $translator->trans('City has been successfully deleted!');
                    $delete = true;
                    $this->addFlash('success', $message);
                }
            }
        }
        return new JsonResponse(['form_delete' => $delete]);
    }
}
