<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;


class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre nom',

                ]
            ])
            ->add('email', TextType::class, [
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre mail',
                ]
            ])
            ->add('phone', TextType::class, [
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre téléphone'
                ]
            ])
            ->add('old_password', PasswordType::class, [
                'mapped' => false,
                'data' => '',
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre mot de passe actuel.'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'constraints'=>[
                    new Regex('/^(?=.{2,}$)(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$/'),
                ],
                'mapped' => false,
                'invalid_message'=> 'le mot de passe et la confirmation doivent être identique',
                'type' => PasswordType::class,
                'required'=>true,
                'first_options'=>[
                    'label' => 'Votre nouveau mot de passe',
                    'attr'=> [
                        'placeholder' => 'Saisir votre nouveau mot de passe '
                    ]
                ],
                'second_options'=>[
                    'label'=>'Confirmez votre nouveau mot de passe',
                    'attr'=>[
                        'placeholder'=>'Merci de confirmer votre nouveau mot de passe'
                    ]
                ]
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
