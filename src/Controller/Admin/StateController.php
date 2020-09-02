<?php

namespace App\Controller\Admin;

use App\Entity\State;
use App\Form\CenterHealthType;
use App\Form\StateType;
use App\Repository\StateRepository;
use App\Repository\TypeVaccinRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    public function register_state(Request $request, TranslatorInterface $translator)
    {
        $stateRequest = $request->request->get('state');
        $nameState = $stateRequest['nameState'];
        $phone = $stateRequest['phoneindic'];
        if ($stateRequest['id'] != '' && $stateRequest['id'] != null){
            $State = $this->stateRepository->find($stateRequest['id']);
            $State->setNameState($nameState);
            $State->setPhoneindic($phone);
            $this->entityManager->persist($State);
            $this->entityManager->flush();
            $message = $translator->trans('The country name has been changed successfully!');
            $this->addFlash('success', $message);
        }else{
            $StateExist = $this->stateRepository->findOneBy(['nameState' => $nameState]);
            if($StateExist){
                $this->addFlash('warning', 'Le nom de pays à été déjà enregistré !');
            }else{
                $stateNew = new State();
                $stateNew->setNameState($nameState);
                $stateNew->setPhoneindic($phone);
                $this->entityManager->persist($stateNew);
                $this->entityManager->flush();

                $message = $translator->trans('The country name has been registered successfully!');
                $this->addFlash('success', $message);
            }
        }
        return $this->redirectToRoute("admin_state");
    }

    /**
     * @Route("/admin/state/remove", name="remove_state", methods={"GET","POST"}, condition="request.isXmlHttpRequest()")
     */
    public function remove_state(Request $request, TranslatorInterface $translator)
    {
        $idState = $request->request->get('id_state');
        $delete = false;
        if ($idState != '' && $idState != null){
            $State = $this->stateRepository->find($idState);
            if (null !=  $State ){
                $Region = $State->getRegions();
                if ($Region  && count($Region) > 0){
                    $delete = false;
                    $message = $translator->trans('Error deleting this element!');
                    $this->addFlash('error', $message);
                }else{
                    $this->entityManager->remove($State);
                    $this->entityManager->flush();
                    $delete = true;
                    $message = $translator->trans('City has been successfully deleted!');
                    $this->addFlash('error', $message);

                }
            }
        }
        return new JsonResponse(['form_delete' => $delete]);
    }

    /**
     * @Route("/upload-excel-state", name="xlsx_import_state")
     */
    public function xlsx_import_state(Request $request, FileUploadService $fileUploadService)
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
                    $pays = $Row['A'];
                    $state = null;
                    if($pays != null) {
                        $state = $this->stateRepository->findOneBy([ 'nameState' => $pays ]);
                        if ($state == null){
                            $state = new State();
                            $state->setNameState($pays);
                            $this->entityManager->persist($state);
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
