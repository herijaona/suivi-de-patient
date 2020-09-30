<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Entity\Region;
use App\Entity\State;
use App\Form\CityType;
use App\Form\RegionType;
use App\Repository\CityRepository;
use App\Repository\RegionRepository;
use App\Repository\StateRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    public function register_city(Request $request,TranslatorInterface $translator){
        $cityRequest = $request->request->get('city');
        $nameCity = $cityRequest['nameCity'];
        $idRegion = $cityRequest['region'];
        $idCity = $cityRequest['id'];
        $departement = $cityRequest['departement'];
        $arrondissement= $cityRequest['arrondissement'];
        $Region = $this->regionRepository->find($idRegion);
        if($idCity != '' && $idCity != null){
            $City = $this->cityRepository->find($idCity);
            $City->setNameCity($nameCity);
            $City->setRegion($Region);
            $City->setArrondissement($arrondissement);
            $City->setDepartement($departement);
            $this->entityManager->persist($City);
            $this->entityManager->flush();
            $message = $translator->trans('modification successfully!');
            $this->addFlash('success', $message);
        }else{
            $CityExist = $this->cityRepository->findOneBy(['nameCity' => $nameCity, 'region' => $Region]);
            if($CityExist){

                $message = $translator->trans('The city name has already been registered!');
                $this->addFlash('warning', $message);
            }else{
                $cityNew = new City();
                $cityNew->setNameCity($nameCity);
                $cityNew->setRegion($Region);
                $cityNew->setDepartement($departement);
                $cityNew->setArrondissement($arrondissement);
                $this->entityManager->persist($cityNew);
                $this->entityManager->flush();
                $message = $translator->trans('The city name has been registered successfully!');
                $this->addFlash('success', $message);
            }
        }
        return $this->redirectToRoute("admin_city");
    }

    /**
     * @Route("/admin/city/remove", name="remove_city", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_city(Request $request, TranslatorInterface $translator)
    {
        $idCity = $request->request->get('id_city');
        $delete = false;
        if ($idCity != '' && $idCity != null){
            $City = $this->cityRepository->find($idCity);
            if (null !=  $City ){
                $CentreHealths = $City->getCentreHealths();
                if ($CentreHealths && count($CentreHealths) > 0){
                    $delete = false;
                    $message = $translator->trans('Error deleting this element!');
                    $this->addFlash('error', $message);
                }else{
                    $this->entityManager->remove($City);
                    $this->entityManager->flush();
                    $delete = true;
                    $message = $translator->trans('City has been successfully deleted!');
                    $this->addFlash('success', $message);
                }
            }
        }

        return new JsonResponse(['form_delete' => $delete]);
    }

    /**
     * @Route("/upload-excel-city", name="xlsx_import")
     * @throws Exception
     */
    public function xlsx_state(Request $request, FileUploadService $fileUploadService)
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
                    $cityOrCommune = $Row['A'];
                    $depatement = $Row['B'];
                    $region = $Row['C'];
                    $pays = $Row['D'];
                    $phone = $Row['E'];
                    $arrondissement = $Row['F'];
                    $state = null;
                    $regions = null;
                    $city = null;
                    if($pays != null) {
                        $state = $this->stateRepository->findOneBy([ 'nameState' => $pays ]);
                        if ($state == null){
                            $state = new State();
                            $state->setNameState($pays);
                            $state->setPhoneindic($phone);
                            $this->entityManager->persist($state);
                            $this->entityManager->flush();
                        }
                    }
                    if($region != null) {
                        $regions = $this->regionRepository->findOneBy([ 'nameRegion' => $region ]);
                        if ($regions == null){
                            $regions = new Region();
                            $regions->setNameRegion($region);
                            $regions->setState($state);
                            $this->entityManager->persist($regions);
                            $this->entityManager->flush();
                        }
                    }

                    if($cityOrCommune != null) {
                        $city = $this->cityRepository->findOneBy([ 'nameCity' => $cityOrCommune ]);
                        if ($city == null){
                            $city = new City();
                            $city->setNameCity($cityOrCommune);
                            $city->setRegion($regions);
                            $city->setDepartement($depatement);
                            $city->setArrondissement($arrondissement);
                            $this->entityManager->persist($city);
                            $this->entityManager->flush();
                        }
                    }

                }
                $i++;
            }

        }

        return new JsonResponse(['form_import' => true]);
    }
}
