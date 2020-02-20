<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('pseudo')
            ->add('telephone')
            ->add('email', EmailType::class)
            ->add('password', TextType::class)
            ->add('password', RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'La confirmation ne correspond pas au mot de passe.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    // required à false pour éviter l'obligation de champ
                    'required' => false,
                    // mapped à false pour ne pas le lier à l'entité user et qu'il ne récupère pas le hash du mdp
                    'mapped' => false,
                    'first_options' => [
                        'label' => 'Nouveau mot de passe',
                    ],
                    'second_options' => [
                        'label' => 'Confirmation mot de passe',
                    ],
                    'row_attr' => ['class' => 'row'],
                    'help_attr' => ['class' => 'helpClass'],
                ])
            // Appel des labels optionnels avec un .first et .second
//            ->add('roles')
            ->add('site');
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
