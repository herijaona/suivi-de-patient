<?php

namespace App\Form;


use App\Entity\Patient;
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
        $builder
            ->add('id', HiddenType::class)
            ->add('dateRdv')
            ->add('heureRdv')
            ->add('patient',EntityType::class,
                ['required' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->orderBy('p.firstName')
                            ->orderBy('p.lastName');
                    },
                    'class' => Patient::class,
                    'attr' => ['class' => 'form-control chosen-select'],
                    'placeholder' => 'Choose a Patient'
                ])
            ->add('description', null, [
                'required' => true,
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
