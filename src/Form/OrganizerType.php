<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Ville;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateDebut')
            ->add('dateCloture')
            ->add('nbInscriptionsMax')
            ->add('descriptionInfos')
            ->add('duree')
//            ->add('dateSortie')
//            ->add('urlPhoto')
//            ->add('site')
//            ->add('etat')
            ->add('lieu')
//            ->add('organisateur')
//            ->add('participants')
            ->add('Valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
