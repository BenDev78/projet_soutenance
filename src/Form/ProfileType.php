<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

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
            ->add('address', TextType::class, [
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre adresse'
                ]
            ])
            ->add('postal_code', IntegerType::class, [
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre code postal'
                ]
            ])
            ->add('city', TextType::class, [
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre ville'
                ]
            ])
            ->add('phone', IntegerType::class, [
                'constraints'=>new Length(10),
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre téléphone'
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
