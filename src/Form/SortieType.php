<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
//            ->add('duree')
//            ->add('dateCloture')
//            ->add('nbInscriptionsMax')
//            ->add('descriptionInfos')
//            ->add('urlPhoto')
            ->add('dateSortie')
            ->add('site')
            ->add('etat')
            ->add('lieu')
            ->add('organisateur')
            ->add('participants')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
