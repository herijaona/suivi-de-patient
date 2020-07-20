<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\TypePatient;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('email')
            ->add('username')
            ->add('lastname')
            ->add('firstname')
            ->add('date_naissance', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('lieu_naissance',EntityType::class ,[
                'class'=>City::class,
                'query_builder'=>function(EntityRepository $entityRepository){
                    return $entityRepository->createQueryBuilder('c');
                },
                'choice_value' => 'id',
                'choice_label' => function(?City $city) {
                    return $city ? strtoupper($city->getNameCity()) : '';
                },
                'placeholder' => 'Lieu de naissance',
            ])
            ->add('phone')
            ->add('address',EntityType::class ,[
                'class'=>City::class,
                'query_builder'=>function(EntityRepository $entityRepository){
                    return $entityRepository->createQueryBuilder('c');
                },
                'choice_value' => 'id',
                'choice_label' => function(?City $city) {
                    return $city ? strtoupper($city->getNameCity()) : '';
                },
                'placeholder' => 'Votre adresse',
            ])
            ->add('type_patient',EntityType::class ,[
                'class'=>TypePatient::class,
                'query_builder'=>function(EntityRepository $entityRepository){
                    return $entityRepository->createQueryBuilder('t');
                },
                'choice_value' => 'id',
                'choice_label' => function(?TypePatient $typePatient) {
                    return $typePatient ? strtoupper($typePatient->getTypePatientName()) : '';
                },
                'placeholder' => 'Type de patient',
            ])
            ->add('namedaddy', null, [
                'required'   => false])
            ->add('namemonther', null, [
                'required'   => false])
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
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
