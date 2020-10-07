<?php

namespace App\Form;

use App\Entity\OrdoConsultation;
use App\Entity\Praticien;
use App\Entity\State;
use App\Entity\Vaccin;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class GenerationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('country', EntityType::class, [
                'required'=>true,
                'class' => State::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('s');
                },
                'choice_value' => 'id',
                'choice_label' => function (?State $state) {
                    return $state ? strtoupper($state->getNameState()) : '';
                },
                'placeholder' => 'Pays',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        /*$resolver->setDefaults([
            'data_class' => OrdoConsultation::class,
        ]);*/
    }
}
