<?php

namespace App\Form;

use App\Entity\CarnetVaccination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarnetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('patient')
            ->add('vaccin')
        ;

        /*switch ($options["type"]) {
            case 'dti':
                $builder->add("datePriseInitiale");
                break;
            case 'rpv':
                $builder->add("rappelVaccin");
                break;
        }*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CarnetVaccination::class,
        ]);
        //$resolver->setRequired(["type"]);
    }
}
