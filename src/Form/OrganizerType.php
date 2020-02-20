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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie :',
            ])
            ->add('dateSortie', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'widget' => 'single_text',
                'years' => range(2020,2050),
                'required' => true,
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée :',
                'required' => true
            ])
            ->add('dateCloture', DateType::class, [
                'label' => 'Date limite d\'inscription :',
                'widget' => 'single_text',
                'years' => range(2020,2050),
                'required' => true,
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de places :',
                'required' => true,
            ])
            ->add('descriptionInfos', TextareaType::class, [
                'label' => 'Description et infos :',
                'required' => false
            ])
            ->add('lieu', EntityType::class, [
                'label' => 'Lieu :',
                'class' => Lieu::class,
                'required' => true,
                'choice_label' => 'nom_lieu',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->orderBy('c.nomLieu', 'ASC');
                }
            ])
            ->add('site', EntityType::class, [
                'label' => "Site :",
                'class' => Site::class,
                'choice_label' => 'nom',
                'required' => true,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->orderBy('c.nom', 'ASC');
                }
            ])
            ->add('Enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary btn-sn submit'
                ]
            ])
            ->add('PublierLaSortie', SubmitType::class, [
                'label' => 'Publier la sortie',
                'attr' => [
                    'class' => 'btn btn-primary btn-sn submit'
                ]
            ]);
//            ->add('Annuler', SubmitType::class, [
//                'label' => 'Annuler',
//                'attr' => [
//                    'class' => 'btn btn-primary btn-sn submit'
//                ]
//            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
