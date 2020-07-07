<?php

namespace App\Form;

use App\Entity\OrdoConsultation;
use App\Entity\Praticien;
use App\Entity\Vaccin;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RdvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $typeRdvArrays = $options['typeRdvArrays'];

        $builder
            ->add('id', HiddenType::class)
            ->add('praticiens',EntityType::class,
                ['required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->orderBy('p.firstName')
                            ->orderBy('p.lastName');
                    },
                    'class' => Praticien::class,
                    'attr' => ['class' => 'form-control chosen-select'],
                    'placeholder' => 'Choisir un Médecin'
                ])
            ->add('dateRdv')
            ->add('heureRdv')
            ->add('description', null, [
                'required' => false,
            ])
            ->add('typeRdv', ChoiceType::class, [
                'choices' => array_flip($typeRdvArrays),
                'required' => true,
            ])
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['typeRdvArrays']);
        /*$resolver->setDefaults([
            'data_class' => OrdoConsultation::class,
        ]);*/
    }
}
