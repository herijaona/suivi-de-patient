<?php

namespace App\Form;

use App\Entity\CentreHealth;
use App\Entity\City;
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

class RegistrationPraticienFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username')
            ->add('lastname')
            ->add('firstname')

            ->add('center_health', EntityType::class, [
                'class' => CentreHealth::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('c');
                },
                'choice_value' => 'id',
                'choice_label' => function (?CentreHealth $centreHealth) {
                    return $centreHealth ? strtoupper($centreHealth->getCentreName()) : '';
                },
                'placeholder' => 'Votre centre de santé',
            ])
            ->add('date_naissance')
            ->add('lieu_naissance')
            ->add('phone')
            ->add('phone_professional')
            ->add('address', TextareaType::class, [
                'attr' => [
                    'rows' => '3'
                ]
            ])
            ->add('country', EntityType::class, [
                'class' => State::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('s');
                },
                'choice_value' => 'id',
                'choice_label' => function (?State $state) {
                    return $state ? strtoupper($state->getNameState()) : '';
                },
                'placeholder' => 'Country',
            ])
            ->add('fonction')
            ->add('sexe', ChoiceType::class, array(
                'choices' => array(
                    'Feminin' => 'Feminin',
                    'Masculin' => 'Masculin'
                ),
                'placeholder' => 'Sexe',
            ))
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
