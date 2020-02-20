<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
        ->add('nomLieu', TextType::class, [
            'label' => 'Nom :'
        ])
        ->add('rue', TextType::class, [
            'label' => 'Rue :'
        ])
        ->add('latitude', TextType::class, [
            'label' => 'Latitude :'
        ])
        ->add('longitude', TextType::class, [
            'label' => 'Longitude :'
        ])
        ->add('ville', EntityType::class, [
            'label' => 'Ville :',
            'class' => Ville::class,
            'choice_label' => 'nomVille',
            'query_builder' => function (EntityRepository $repository) {
                return $repository->createQueryBuilder('c')->orderBy('c.nomVille', 'ASC');
            }
        ])
        ->add('enregistrer', SubmitType::class, [
            'label' => 'Ajouter',
            'attr' => [
                'class' => 'btn btn-outline-dark btn-soumission',
                'id' => 'btn-soumission-profil'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
