<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('rating', IntegerType::class, ['label' => 'Note Générale (0 : Imbuvable, 1 : Très mauvais, 2 : Mauvais, 3 : Bon, 4 : Très bon, 5 : Excellent)'])
            ->add('comment', TextareaType::class, ['label' => 'Commentaire'])
            ->add('pseudo', TextType::class, ['label' => 'Pseudo'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}





