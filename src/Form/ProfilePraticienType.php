<?php

namespace App\Form;

use App\Entity\CentreHealth;
use App\Entity\City;
use App\Entity\Fonction;
use App\Entity\State;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ProfilePraticienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class,[
                'required'=>false
            ])

            ->add('center_health', EntityType::class, [
                'class' => CentreHealth::class,

                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('c');
                },
                'choice_value' => 'id',
                'required'   => false,

                'choice_label' => function (?CentreHealth $centreHealth) {
                    return $centreHealth ? strtoupper($centreHealth->getCentreName()) : '';
                },
                'placeholder' => 'Votre centre de santé',
            ])

            ->add('address', TextareaType::class, [
                'attr' => [
                    'rows' => '3'
                ],
                'required'=> false

            ])
            ->add('CountryOnBorn', EntityType::class, [
                'class' => State::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('s');
                },
                'required'=>false,
                'choice_value' => 'id',
                'choice_label' => function (?State $state) {
                    return $state ? strtoupper($state->getNameState()) : '';
                },
                'placeholder' => 'Pays de Naissance',
            ])
            ->add('country', EntityType::class, [
                'class' => State::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('s');
                },
                'required'=>true,
                'choice_value' => 'id',
                'choice_label' => function (?State $state) {
                    return $state ? strtoupper($state->getNameState()) : '';
                },
                'placeholder' => 'Pays de Fonction',
            ])
            ->add('fonction', EntityType::class, [
                'class' => Fonction::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('s');
                },
                'required'=>false,
                'choice_value' => 'id',
                'choice_label' => function (?Fonction $fonction) {
                    return $fonction ? strtoupper($fonction->getNomFonction()) : '';
                },
                'placeholder' => 'Votre Fonction',
            ])

          ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
