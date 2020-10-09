<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RdvAssocieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $patient = $options['patient'];
        $typeRdvArrays = $options['typeRdvArrays'];
        $builder
            ->add('id', HiddenType::class)

            ->add('patient', ChoiceType::class, [
                'choices' => array_flip($patient),
                'required' => true,
                'placeholder' => 'Choisir Patient'
            ])
            ->add('typeRdv', ChoiceType::class, [
                'choices' => array_flip($typeRdvArrays),
                'required' => true,
            ])
            ->add('dateRdv')
            ->add('heureRdv')
            ->add('objet');


    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['typeRdvArrays']);
        $resolver->setRequired(['patient']);


    }
}
