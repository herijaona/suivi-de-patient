<?php

namespace App\Form;

use App\Entity\CentreHealth;
use App\Entity\CentreType;
use App\Entity\City;
use App\Entity\Region;
use App\Entity\State;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CenterHealthType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('centreName', null, [
                'required' => true,
            ])
            ->add('centrePhone')
            ->add('responsableCentre')
            ->add('centretype', EntityType::class,
                [
                    'required' => true,
                    'class' => CentreType::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')->orderBy('c.typeName');
                    },
                    'attr' => ['class' => 'form-control chosen-select'],
                    'placeholder' => 'Choisir Type de centre'
                ])
            //->add('address')
           /* ->add('pays', EntityType::class,
                ['required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->orderBy('p.nameState');
                    },
                    'class' => State::class,
                    'attr' => ['onchange' => 'return changeState(this);', 'class' => 'form-control chosen-select'],
                    'placeholder' => 'Choisir Pays'
                ])
            ->add('region', EntityType::class,
                ['required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('r')->join('r.state', 's')
                            ->orderBy('r.nameRegion')
                            ;
                    },
                    'class' => Region::class,
                    'attr' => ['onchange' => 'return changeRegion(this);', 'class' => 'form-control chosen-select'],
                    'placeholder' => 'Choisir Région'
                ])*/
            ->add('ville', EntityType::class,
                ['required' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('v')
                            ->join('v.region', 'r')
                            ->join('r.state', 's')
                            ->orderBy('v.nameCity')
                            ;
                    },
                    'class' => City::class,
                    'attr' => ['class' => ' form-control'],
                    'placeholder' => 'Choisir Ville'
                ])
            ->add('numRue')
            ->add('quartier')
        ;
        //$builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }

    public function onPreSetData(FormEvent $event) {
        $center =  $event->getData();
        $form = $event->getForm();
        if ($center != NULL && NULL != $center['pays']) {
            $pays = $center['pays'];
            $form->add('region', EntityType::class, [
                'class' => Region::class,
                'placeholder' => 'Choisir Region',
                'query_builder' => function (EntityRepository $er) use ($pays) {
                    return $er->createQueryBuilder('r')
                        ->join('r.state', 's')
                        ->where('s.id = :pays')
                        ->setParameter('pays', $pays)
                        ->orderBy('r.nameRegion')
                        ;
                },
            ]);
        }
        if ($center != NULL && NULL != $center['region']) {
            $region = $center['region'];
            $form->add('ville', EntityType::class, [
                'class' => City::class,
                'placeholder' => 'Choisir Ville',
                'query_builder' => function (EntityRepository $er) use ($region) {
                    return $er->createQueryBuilder('v')
                        ->join('v.region', 'r')
                        ->join('r.state', 's')
                        ->where('r.id = :region')
                        ->setParameter('region', $region)
                        ->orderBy('v.nameCity')
                        ;
                },
            ]);
        }
    }
}
