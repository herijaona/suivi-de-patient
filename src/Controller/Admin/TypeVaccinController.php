<?php

namespace App\Controller\Admin;

use App\Repository\TypeVaccinRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TypeVaccinController extends AbstractController
{
    protected $typeVaccinRepository;

    function __construct(
        TypeVaccinRepository $typeVaccinRepository
    )
    {
        $this->typeVaccinRepository = $typeVaccinRepository;
    }

    /**
     * @Route("/admin/type/vaccin", name="admin_type_vaccin")
     */
    public function index()
    {
        $typeVaccin = $this->typeVaccinRepository->findAll();
        return $this->render('admin/type_vaccin/index.html.twig', [
            'typeVaccin' => $typeVaccin,
        ]);
    }
}
