<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Entity\Region;
use App\Form\CityType;
use App\Form\RegionType;
use App\Repository\CityRepository;
use App\Repository\RegionRepository;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{

    protected $cityRepository;
    protected $regionRepository;
    protected $entityManager;
    protected $stateRepository;

    function __construct(
        CityRepository $cityRepository,
        RegionRepository $regionRepository,
        StateRepository $stateRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->cityRepository = $cityRepository;
        $this->regionRepository = $regionRepository;
        $this->entityManager = $entityManager;
        $this->stateRepository = $stateRepository;
    }


    /**
     * @Route("/admin/city", name="admin_city")
     */
    public function index()
    {
        $cities = $this->cityRepository->findAll();
        return $this->render('admin/city/index.html.twig', [
            'cities' => $cities,
        ]);
    }

    /**
     * @Route("/admin/city/add", name="add_form_city", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function add_form_city(Request $request)
    {
        $action = $request->request->get('action');
        $idCity = $request->request->get('id_city');
        $eventData = [];
        //$form = $this->createForm(StateType::class, $eventData);
        if ($action == "new") {
            $city = new City();
            $form = $this->createForm(CityType::class, $city);
            $response = $this->renderView('admin/city/new_form_city.html.twig', [
                'new' => true,
                'form' => $form->createView(),
                'eventData' => $eventData,
            ]);
        } else {
            $City = $this->cityRepository->find($idCity);
            $form = $this->createForm(CityType::class, $City);
            $response = $this->renderView('admin/city/new_form_city.html.twig', [
                'new' => false,
                'form' => $form->createView(),
                'eventData' => $eventData,
            ]);
        }
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);
    }


    /**
     * @Route("/city/register", name="register_city", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function register_city(Request $request)
    {
        $cityRequest = $request->request->get('city');
        $nameCity = $cityRequest['nameCity'];
        $idRegion = $cityRequest['region'];
        $idCity = $cityRequest['id'];
        $Region = $this->regionRepository->find($idRegion);
        if($idCity != '' && $idCity != null){
            $City = $this->cityRepository->find($idCity);
            $City->setNameCity($nameCity);
            $City->setRegion($Region);
            $this->entityManager->persist($City);
            $this->entityManager->flush();
            $this->addFlash('success', 'modification avec succès !');
        }else{
            $CityExist = $this->cityRepository->findOneBy(['nameCity' => $nameCity, 'region' => $Region]);
            if($CityExist){
                $this->addFlash('warning', 'Le nom de ville à été déjà enregistré  !');
            }else{
                $cityNew = new City();
                $cityNew->setNameCity($nameCity);
                $cityNew->setRegion($Region);
                $this->entityManager->persist($cityNew);
                $this->entityManager->flush();
                $this->addFlash('success', 'Le nom de ville à été enregistré avec succès !');
            }
        }
        return $this->redirectToRoute("admin_city");
    }

    /**
     * @Route("/admin/city/remove", name="remove_city", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_city(Request $request)
    {
        $idCity = $request->request->get('id_city');
        $delete = false;
        if ($idCity != '' && $idCity != null){
            $City = $this->cityRepository->find($idCity);
            if (null !=  $City ){
                $CentreHealths = $City->getCentreHealths();
                if ($CentreHealths && count($CentreHealths) > 0){
                    $delete = false;
                    $this->addFlash('error', 'Erreur de suprimé de cet élément !');
                }else{
                    $this->entityManager->remove($City);
                    $this->entityManager->flush();
                    $delete = true;
                    $this->addFlash('success', 'ville à été supprimé avec succès !');
                }
            }
        }

        return new JsonResponse(['form_delete' => $delete]);
    }
}
