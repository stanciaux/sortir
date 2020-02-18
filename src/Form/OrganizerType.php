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
        $builder

            ->add('nom', TextType::class)
            ->add('dateSortie', DateTimeType::class, [
                'label' => 'Date de la sortie',
//                'attr' => [
//                    'class' => 'form-control datetimepicker-input',
//                    'data-toggle' => 'datetimepicker',
//                    'data-target' => '#sortie_dateDebut'
//                ],
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
                'choice_label' => 'nom',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->orderBy('c.nom', 'ASC');
                }
            ])
            ->add('Enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-dark btn-sn'
                ]
            ])
            ->add('PublierLaSortie', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'class' => 'btn btn w-100'
                ]
            ])
            ->add('Annuler', SubmitType::class, [
                'label' => 'Annuler',
                'attr' => [
                    'class' => 'btn btn w-100'
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
