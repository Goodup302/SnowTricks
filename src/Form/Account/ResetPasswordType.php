<?php

namespace App\Form\Account;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class,[
                'label' => 'Répéter mot de passe',
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répéter mot de passe'],
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Réinitialiser'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}