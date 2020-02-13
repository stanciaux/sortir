<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')

//            ->add('dateDebut')
            ->add('duree')
            ->add('dateCloture')
            ->add('nbInscriptionsMax')
            ->add('descriptionInfos')
            ->add('urlPhoto')
            ->add('dateSortie')
            ->add('site')
            ->add('etat')
            ->add('lieu')
            ->add('organisateur')
            ->add('participants')
            ->add('Valider', SubmitType::class)

            ->add('lieu', LieuType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
