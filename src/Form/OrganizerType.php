<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder
//            ->add('nom', TextType::class)
//
//            ->add('dateSortie', DateType::class, array('label' => "Date de la sortie : "))
//            ->add('dateCloture', DateType::class, array('label' => "Date limite d'inscription : "))
//            ->add('duree', NumberType::class, array('label' => 'Durée : '))
//            ->add('nbInscriptionsMax', NumberType::class, array('label' => 'Nombre de places : '))
//            ->add('descriptionInfos', TextareaType::class, array('label' => "Description et infos : "))
//            ->add('lieu', ChoiceType::class, array('label' => "Lieu : "))
//            ->add('Valider', SubmitType::class)
////            ->add('urlPhoto')
//
////            ->add('site', HiddenType::class, [
////                'data' => '',
////            ]);
////            ->add('etat', HiddenType::class, [
////                'data' => 'ouverte',
////            ]);
//////            ->add('lieu')
////            ->add('organisateur')
////            ->add('participants')
//
////
////            ->add('lieu', LieuType::class)
//        ;
//    }
        $builder
            ->add('nom', TextType::class)
            ->add('dateSortie', TextType::class, [
                'label' => 'Date Evenement (début)',
                'attr' => [
                    'class' => 'form-control datetimepicker-input',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#sortie_dateDebut'
                ],
                'required' => true,
                'mapped' => false
            ])
            ->add('duree', IntegerType::class)
            ->add('dateCloture', TextType::class, [
                'label' => 'Date Cloture inscription',
                'attr' => [
                    'class' => 'form-control datetimepicker-input',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#sortie_dateCloture'
                ],
                'required' => true,
                'mapped' => false
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre d\'inscription maximum'
            ])
            ->add('descriptionInfos', TextType::class, [
                'label' => 'Description'
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom_lieu',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->orderBy('c.nomLieu', 'ASC');
                }
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom_site',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->orderBy('c.nomSite', 'ASC');
                }
            ])
            ->add('Enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-success w-100'
                ]
            ])
            ->add('Publier la sortie', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'class' => 'btn btn-primary w-100'
                ]
            ])
            ->add('Annuler', SubmitType::class, [
                'label' => 'Annuler',
                'attr' => [
                    'class' => 'btn btn-danger w-100'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
