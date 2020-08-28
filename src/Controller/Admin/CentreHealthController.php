<?php

namespace App\Controller\Admin;

use App\Entity\CentreHealth;
use App\Entity\VaccinCentreHealth;
use App\Form\CenterHealthType;
use App\Entity\Region;
use App\Entity\State;
use App\Entity\City;
use App\Entity\CentreType;
use App\Repository\CentreHealthRepository;
use App\Repository\CentreTypeRepository;
use App\Repository\CityRepository;
use App\Repository\RegionRepository;
use App\Repository\StateRepository;
use App\Repository\VaccinCentreHealthRepository;
use App\Repository\VaccinRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Proxies\__CG__\App\Entity\CentreType as EntityCentreType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class CentreHealthController extends AbstractController
{
    protected $centreHealthRepository;
    protected $stateRepository;
    protected $regionRepository;
    protected $cityRepository;
    protected $centreTypeRepository;
    protected $vaccinRepository;
    protected $vaccinCentreHealthRepository;
    protected $entityManager;

    function __construct(
        CentreHealthRepository $centreHealthRepository,
        StateRepository $stateRepository,
        RegionRepository $regionRepository,
        CityRepository $cityRepository,
        CentreTypeRepository $centreTypeRepository,
        EntityManagerInterface $entityManager,
        VaccinRepository $vaccinRepository,
        VaccinCentreHealthRepository $vaccinCentreHealthRepository
    )
    {
        $this->centreHealthRepository = $centreHealthRepository;
        $this->stateRepository = $stateRepository;
        $this->regionRepository = $regionRepository;
        $this->cityRepository = $cityRepository;
        $this->centreTypeRepository = $centreTypeRepository;
        $this->vaccinRepository = $vaccinRepository;
        $this->vaccinCentreHealthRepository = $vaccinCentreHealthRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/centre/health", name="admin_centre_health")
     */
    public function index()
    {
        $centreSante = $this->centreHealthRepository->findAll();
        $vaccinActive = $this->vaccinRepository->findBy(['etat' => true], ['vaccinName' => 'ASC']);
        return $this->render('admin/centre_health/index.html.twig', [
            'centreSante' => $centreSante,
            'vaccinActive' => $vaccinActive
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
    public function register_center_health(Request $request,TranslatorInterface $translator)
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
            $message = $translator->trans('The health center has been successfully modified!');
            $this->addFlash('success', $message);
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
            $message = $translator->trans('The health center has been successfully registered!');
            $this->addFlash('success', $message);
        }
        return $this->redirectToRoute("admin_centre_health");
    }

    /**
     * @Route("/admin/center-health/remove", name="remove_center_health", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_city(Request $request,TranslatorInterface $translator)
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
                    $message = $translator->trans('City has been successfully deleted!');
                    $delete = true;
                    $this->addFlash('success', $message);
                }
            }
        }

        return new JsonResponse(['form_delete' => $delete]);
    }

    /**
     * @Route("/admin/center-health/affected-vaccin", name="affected_center_health", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function affected_center_health(Request $request,TranslatorInterface $translator)
    {
        $center_healths = $request->request->get('center_health');
        $vaccin_center_healths = $request->request->get('vaccin_center_health');


        if ($center_healths && count($center_healths) > 0  ){
            foreach ($center_healths as $center_health ){
                $CenterHealth = $this->centreHealthRepository->find((int)$center_health);
                if ($vaccin_center_healths && count($vaccin_center_healths) > 0 ){
                    foreach ($vaccin_center_healths as $vaccin_center_health){
                        $Vaccin = $this->vaccinRepository->find((int)$vaccin_center_health);
                        if ($this->vaccinCentreHealthRepository->findOneBy(['centreHealth' => $CenterHealth , 'vaccin' => $Vaccin ]) == null){
                            $vaccinCentreHealth = new VaccinCentreHealth();
                            $vaccinCentreHealth->setCentreHealth($CenterHealth);
                            $vaccinCentreHealth->setVaccin($Vaccin);
                            $this->entityManager->persist($vaccinCentreHealth);
                            $this->entityManager->flush();
                            $message = $translator->trans('Assignment successfully!');
                            $this->addFlash('success', $message);
                        }
                    }
                }
            }
        }

        return new JsonResponse(['form_success' => 'OK']);
    }

    
    /**
     * @Route("/upload-excel-center-health", name="xlsx_import_center_health")
     */
    public function xlsx_import_center_health(Request $request, FileUploadService $fileUploadService)
    {
        $fileFolder =  $this->getParameter('import_directory');
        $files = $request->files->get("file");
        $filePathName = $fileUploadService->upload($files);
        //$filePathName = md5(uniqid()) . $file->getClientOriginalName();
        if ($filePathName != null){
            $spreadsheet = IOFactory::load($fileFolder ."/". $filePathName); // Here we are able to read from the excel file
            $row = $spreadsheet->getActiveSheet()->removeRow(1); // I added this to be able to remove the first file line
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // here, the read data is turned into an array
            $i = 0;
            foreach ($sheetData as $Row)
            {
                if ($i != 0){
                    $IdCentre = $Row['A'];
                    $NomCentre = $Row['B'];
                    $VilleCentre = $Row['C'];
                    $RégionCentre = $Row['D'];
                    $PaysCentre = $Row['E'];
                    $TypeCentre = $Row['F'];
                    $TelephoneCentre = $Row['G'];
                    $ReferentCentre = $Row['H'];
                    $Responsable = $Row['I'];
                    $NumRueCentre = $Row['J'];
                    $QuartierCentre = $Row['K'];

                    $state = null;
                    $regions = null;
                    $city = null;
                    $typeCenter = null;
                    $CenterHealth = null;



                    if($PaysCentre != null) {
                        $state = $this->stateRepository->findOneBy([ 'nameState' => $PaysCentre ]);
                        if ($state == null){
                            $state = new State();
                            $state->setNameState($PaysCentre);
                            $this->entityManager->persist($state);
                        }
                    }
                    if($RégionCentre != null) {
                        $regions = $this->regionRepository->findOneBy([ 'nameRegion' => $RégionCentre ]);
                        if ($regions == null){
                            $regions = new Region();
                            $regions->setNameRegion($RégionCentre);
                            $regions->setState($state);
                            $this->entityManager->persist($regions);
                        }
                    }

                    if($VilleCentre != null) {
                        $city = $this->cityRepository->findOneBy([ 'nameCity' => $VilleCentre ]);
                        if ($city == null){
                            $city = new City();
                            $city->setNameCity($VilleCentre);
                            $city->setRegion($regions);
                            $this->entityManager->persist($city);
                        }
                    }

                    if($TypeCentre != null) {
                        $typeCenter = $this->centreTypeRepository->findOneBy([ 'typeName' => $TypeCentre ]);
                        if ($typeCenter == null){
                            $typeCenter = new CentreType();
                            $typeCenter->setTypeName($TypeCentre);
                            $this->entityManager->persist($typeCenter);
                        }
                    }

                    if($NomCentre  != null) {
                        $CenterHealth = $this->centreHealthRepository->findOneBy([ 'centreName' => $NomCentre ]);
                        if ($CenterHealth == null){
                            $CenterHealth = new CentreHealth();
                            $CenterHealth->setCentreName($NomCentre);
                            $CenterHealth->setCentrePhone($TelephoneCentre);
                            $CenterHealth->setResponsableCentre($Responsable);
                            $CenterHealth->setCentreReferent($ReferentCentre);
                            $CenterHealth->setNumRue($NumRueCentre);
                            $CenterHealth->setQuartier($QuartierCentre);
                            $CenterHealth->setCentreType($typeCenter);
                            $CenterHealth->setCity($city);
                            $this->entityManager->persist($CenterHealth);
                        }
                    }

                    $this->entityManager->flush();
                }
                $i++;
            }

        }

        return new JsonResponse(['form_import' => true]);
    }
}
