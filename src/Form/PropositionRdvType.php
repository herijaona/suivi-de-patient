<?php

namespace App\Form;


use App\Entity\Patient;
use App\Entity\Vaccin;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropositionRdvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $patient = $options['patient'];
        $typeRdvArrays = $options['typeRdvArrays'];
        $builder
            ->add('id', HiddenType::class)
            ->add('dateRdv')
            ->add('patient', ChoiceType::class, [
                'choices' => array_flip($patient),
                'required' => true,
                'placeholder' => 'Choisir Patient'
            ])
            ->add('typeRdv', ChoiceType::class, [
                'choices' => array_flip($typeRdvArrays),
                'required' => true,
            ])
            ->add('heureRdv')
            ->add('vaccin', EntityType::class,
                ['required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('v')
                            ->orderBy('v.vaccinName');
                    },
                    'class' => Vaccin::class,
                    'attr' => ['class' => 'form-control chosen-select'],
                    'placeholder' => 'Choisir vaccin'
                ])
            ->add('description', null, [
                'required' => false,
            ]);


    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['typeRdvArrays']);
        $resolver->setRequired(['patient']);


    }
}
