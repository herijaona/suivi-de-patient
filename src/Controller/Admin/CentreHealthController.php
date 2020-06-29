<?php

namespace App\Controller\Admin;

use App\Entity\CentreHealth;
use App\Form\CenterHealthType;
use App\Repository\CentreHealthRepository;
use App\Repository\CentreTypeRepository;
use App\Repository\CityRepository;
use App\Repository\RegionRepository;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CentreHealthController extends AbstractController
{
    protected $centreHealthRepository;
    protected $stateRepository;
    protected $regionRepository;
    protected $cityRepository;
    protected $centreTypeRepository;
    protected $entityManager;

    function __construct(
        CentreHealthRepository $centreHealthRepository,
        StateRepository $stateRepository,
        RegionRepository $regionRepository,
        CityRepository $cityRepository,
        CentreTypeRepository $centreTypeRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->centreHealthRepository = $centreHealthRepository;
        $this->stateRepository = $stateRepository;
        $this->regionRepository = $regionRepository;
        $this->cityRepository = $cityRepository;
        $this->centreTypeRepository = $centreTypeRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/centre/health", name="admin_centre_health")
     */
    public function index()
    {
        $centreSante = $this->centreHealthRepository->findAll();
        return $this->render('admin/centre_health/index.html.twig', [
            'centreSante' => $centreSante,
        ]);
    }

    /**
     * @Route("/form-add-center-health", name="add_form_center_health", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @return JsonResponse
     */
    public function add_form_center_health(Request $request)
    {
        $action = $request->request->get('action');
        $idHealthCenter = $request->request->get('id_health_center');
        $eventData = [];

        if ($action == "new") {
            $form = $this->createForm(CenterHealthType::class, $eventData);
            $response = $this->renderView('admin/centre_health/new_form_center_health.html.twig', [
                'new' => true,
                'form' => $form->createView(),
                'eventData' => $eventData,
            ]);
        } else {
            $centreHealth = $this->centreHealthRepository->find($idHealthCenter);
            $eventData['id'] = $centreHealth->getId();
            $eventData['centreName'] = $centreHealth->getCentreName();
            $eventData['centrePhone'] = $centreHealth->getCentrePhone();
            $eventData['responsableCentre'] = $centreHealth->getResponsableCentre();
            $eventData['centretype'] = $centreHealth->getCentreType();
            $eventData['ville'] = $centreHealth->getCity();
            $eventData['numRue'] = $centreHealth->getNumRue();
            $eventData['quartier'] = $centreHealth->getQuartier();
            $form = $this->createForm(CenterHealthType::class, $eventData);
            $response = $this->renderView('admin/centre_health/new_form_center_health.html.twig', [
                'new' => false,
                'form' => $form->createView(),
                'eventData' => $eventData,
            ]);
        }
        $form->handleRequest($request);
        return new JsonResponse(['form_html' => $response]);
    }

    /**
     * @Route("/center-health/select-state", name="select_state_center_health", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function changeStateModal(Request $request)
    {
        $idState = $request->request->get('idState');
        $state = $this->stateRepository->find($idState);
        $regions = $this->regionRepository->findBy(['state' =>$state]);
        $tabRegion = [];
        if ($regions) {
            $k=0;
            foreach ($regions as $region) {
                $tabRegion[$region->getId()] = $region->getNameRegion();
                $k++;
            }
        }
        return new JsonResponse($tabRegion);
    }

    /**
     * @Route("/center-health/select-region", name="select_region_center_health", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function changeRegionModal(Request $request)
    {
        $idRegion = $request->request->get('idRegion');
        $region = $this->regionRepository->find($idRegion);
        $cities = $this->cityRepository->findBy(['region' =>$region]);
        $tabCity = [];
        if ($cities) {
            $k=0;
            foreach ($cities as $city) {
                $tabCity[$city->getId()] = $city->getNameCity();
                $k++;
            }
        }
        return new JsonResponse($tabCity);
    }

    /**
     * @Route("/center-health/register", name="register_center_health", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function register_center_health(Request $request)
    {
        $center_healthRequest = $request->request->get('center_health');

        $idCenterType = $center_healthRequest['centretype'];
        $idCity = $center_healthRequest['ville'];
        $CenterType = $this->centreTypeRepository->find($idCenterType);
        $City = $this->cityRepository->find($idCity);
        if($center_healthRequest['id'] != '' && $center_healthRequest['id'] != null){
            $centreHealth = $this->centreHealthRepository->find($center_healthRequest['id']);
            $centreHealth->setCentreName($center_healthRequest['centreName']);
            $centreHealth->setCentrePhone($center_healthRequest['centrePhone']);
            $centreHealth->setResponsableCentre($center_healthRequest['responsableCentre']);
            $centreHealth->setCentreType($CenterType);
            $centreHealth->setCity($City);
            $centreHealth->setNumRue($center_healthRequest['numRue']);
            $centreHealth->setQuartier($center_healthRequest['quartier']);
            $this->entityManager->persist($centreHealth);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le centre de santé à été modifié avec succès !');
        }
        else{
            $centerHealth = new CentreHealth();
            $centerHealth->setCentreName($center_healthRequest['centreName']);
            $centerHealth->setCentrePhone($center_healthRequest['centrePhone']);
            $centerHealth->setResponsableCentre($center_healthRequest['responsableCentre']);
            $centerHealth->setCentreType($CenterType);
            $centerHealth->setCity($City);
            $centerHealth->setNumRue($center_healthRequest['numRue']);
            $centerHealth->setQuartier($center_healthRequest['quartier']);
            $this->entityManager->persist($centerHealth);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le centre de santé à été enregistré avec succès !');
        }
        return $this->redirectToRoute("admin_centre_health");
    }

    /**
     * @Route("/admin/center-health/remove", name="remove_center_health", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_city(Request $request)
    {
        $idCenterHealth = $request->request->get('id_center_health');
        $delete = false;

        if ($idCenterHealth != '' && $idCenterHealth != null){
            $CenterHealth = $this->centreHealthRepository->find($idCenterHealth);
            if (null !=  $CenterHealth ){
                $VaccinCentreHealths = $CenterHealth->getVaccinCentreHealths();
                if ($VaccinCentreHealths && count($VaccinCentreHealths) > 0){
                    $delete = false;
                    $this->addFlash('error', 'Erreur de suprimé de cet élément !');
                }else{
                    $this->entityManager->remove($CenterHealth);
                    $this->entityManager->flush();
                    $delete = true;
                    $this->addFlash('success', 'ville à été supprimé avec succès !');
                }
            }
        }

        return new JsonResponse(['form_delete' => $delete]);
    }
}
