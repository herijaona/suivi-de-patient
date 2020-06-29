<?php

namespace App\Form;

use App\Entity\Vaccin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VaccinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('vaccinName', null, [
                'required'    => true
            ])
            ->add('vaccinDescription')
            ->add('etat', null, [
                'label'    => 'Status'
            ])
            ->add('datePriseInitiale')
            ->add('rappel1')
            ->add('rappel2')
            ->add('rappel3')
            ->add('rappel4')
            ->add('rappel5')
            ->add('rappel6')
            ->add('rappel7')
            ->add('rappel8')
            ->add('rappel9')
            ->add('rappel10')
            ->add('TypeVaccin', null, [
                'required'    => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vaccin::class,
        ]);
    }
}
