<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'constraints'=>[
                    new Regex('/^(?=.{2,}$)(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$/'),
                ],
                'invalid_message'=> 'le mot de passe et la confirmation doivent être identique',
                'type' => PasswordType::class,
                'required'=>true,
                'first_options'=>[
                    'label' => 'Mot de passe',
                    'attr'=> [
                        'placeholder' => 'Saisir votre mot de passe '
                    ]
                ],
                'second_options'=>[
                    'label'=>'Confirmez votre mot de passe',
                    'attr'=>[
                        'placeholder'=>'Merci de confirmer votre mot de passe'
                    ]
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
