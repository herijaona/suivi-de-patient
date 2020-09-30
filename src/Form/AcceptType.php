<?php

namespace App\Form;

use App\Entity\Praticien;
use App\Entity\Vaccin;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AcceptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('id', HiddenType::class)
            ->add('dateRdv')
            ->add('heureRdv')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        /*$resolver->setDefaults([
            'data_class' => OrdoConsultation::class,
        ]);*/
    }
}
