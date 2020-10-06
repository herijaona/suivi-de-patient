<?php

namespace App\Form;

use App\Entity\CentreHealth;
use App\Entity\City;
use App\Entity\Fonction;
use App\Entity\Patient;
use App\Entity\State;
use App\Entity\User;
use App\Entity\Vaccin;
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

class GenerationVaccinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $patient = $options['patient'];
        $builder


            ->add('patient', ChoiceType::class, [
                'choices' => array_flip($patient),
                'required' => true,
                'placeholder' => 'Choisir Patient'
            ])
             ->add('vaccin', EntityType::class, [
                'class' => Vaccin::class,

                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('p');
                },
                'choice_value' => 'id',
                'required'   => true,

                'choice_label' => function (?Vaccin $vaccin) {
                    return $vaccin ? strtoupper($vaccin->getVaccinName()) : '';
                },
                'placeholder' => 'Vaccin',
            ])
            ->add('identification')
            ->add('date_prise');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['patient']);

    }
}
