<?php

namespace App\Form;

use App\Entity\CarnetVaccination;
use App\Entity\Fonction;
use App\Entity\Praticien;
use App\Repository\FonctionRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarnetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('patient')
            ->add('vaccin')
            ->add('date')
            ->add('heure')
            ->add('carnet')
            ->add('praticien', EntityType::class, [
                'required'=>false,
                'class' => Fonction::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('p');
                },
                'choice_value' => 'Praticien.id',
                'choice_label' => function (?Fonction $fonction) {
                    return $fonction ? strtoupper($fonction->getPraticien()->getLastName().'  '.$fonction->getPraticien()->getFirstName().' '.$fonction->getFonction()) : '';
                },
                'placeholder' => 'Praticien',
            ])


        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {

        //$resolver->setRequired(["type"]);
    }
}
