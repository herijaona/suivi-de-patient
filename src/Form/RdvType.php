<?php

namespace App\Form;

use App\Entity\Fonction;
use App\Entity\OrdoConsultation;
use App\Entity\Praticien;
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


class RdvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $typeRdvArrays = $options['typeRdvArrays'];
        $builder
            ->add('id', HiddenType::class)
            ->add('fonction',EntityType::class,
                array('required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('f');
                        },

                    'class' => Fonction::class,
                    'choice_label' => function (?Fonction $fonction) {
                        return $fonction ? strtoupper($fonction->getFonction()) : '';
                    },
                    'attr' => array('class' => 'form-control chosen-select'),
                    'placeholder' => 'Fonction'
                ))
            ->add('dateRdv',null,[
                'required'=> false
                ])
            ->add('heureRdv',null,[
                'required'=> false
            ])
            ->add('objet')
            ->add('typeRdv', ChoiceType::class, [
                'choices' => array_flip($typeRdvArrays),
                'required' => true,
            ])
            ->add('Associer', CheckboxType::class, [
                'label'    => 'associer',
                'required' => false,
            ]);
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
