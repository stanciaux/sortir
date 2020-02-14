<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder
//            ->add('nomVille')
//            ->add('codePostal')
//        ;
//    }
        $builder
            ->add('nomVille', TextType::class)
            ->add('codePostal', TextType::class)
            ->add('submit',SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => [
                    'class' => 'btn btn-primary w-100'
                ]
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}
