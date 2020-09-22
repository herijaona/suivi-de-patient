<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\State;
use App\Entity\TypePatient;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username',null, [
                'required'   => false
            ])
            ->add('lastname',null, [
                'required'   => false
            ])
            ->add('firstname',null, [
                'required'   => false
            ])
            ->add('date_naissance',null, [
                'required'   => false
            ])
            ->add('phone')
            ->add('address', TextareaType::class, [
                'attr' => [
                    'rows' => '3'
                ]
            ])
            ->add('type_patient', EntityType::class, [
                'class' => TypePatient::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('t');
                },
                'choice_value' => 'id',
                'choice_label' => function (?TypePatient $typePatient) {
                    return $typePatient ? strtoupper($typePatient->getTypePatientName()) : '';
                },
                'placeholder' => 'Type de patient',
            ])
            ->add('namedaddy', null, [
                'required'   => false
            ])
            ->add('namemonther', null, [
                'required'   => false
            ])


            ->add('sexe', ChoiceType::class, array(
                'choices' => array(
                    'Feminin' => 'Feminin',
                    'Masculin' => 'Masculin'
                ),
                'required'=>false,
                'placeholder' => 'Sexe',
            ))
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'required'=>false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit être au moins {{ limit }} caractère',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('country', EntityType::class, [
                'required'=>false,
                'class' => State::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('s');
                },
                'choice_value' => 'id',
                'choice_label' => function (?State $state) {
                    return $state ? strtoupper($state->getNameState()) : '';
                },
                'placeholder' => 'Choisir Votre Pays de Résidence',
            ])

        ->add('enceinte', ChoiceType::class, [
        'choices'  => [
            'Oui' => 'true',
            'Non' => 'false'
        ],
        'expanded' => true,
        'multiple' => false,
    ]);

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
