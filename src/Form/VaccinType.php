<?php

namespace App\Form;

use App\Entity\State;
use App\Entity\Vaccin;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('vaccinDescription',null,[
                'required' =>true
            ])
            ->add('etat', null, [
                'label'    => 'Etat'
            ])
            ->add('IdVaccin',null,[
                'required'=>true
            ])
            ->add('state', EntityType::class,[
                'class' => State::class,
                'query_builder'=>function(EntityRepository $entityRepository){
                    return $entityRepository->createQueryBuilder('s');
                },
                'choice_value' => 'id',
                'choice_label' => function(?State $state){
                    return $state ? strtoupper($state->getNameState()):'';
                },
                'placeholder' => 'Pays du Vaccin',
            ])
            ->add('datePrise',null,[
                'required'=> true
            ])
            ->add('Statut',null,[
                'required'=> true
            ])

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
