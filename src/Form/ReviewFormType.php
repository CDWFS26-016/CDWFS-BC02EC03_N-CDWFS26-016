<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Votre avis',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Partagez votre expérience...'
                ]
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Votre note',
                'choices' => [
                    '⭐ 1 - Très mauvais' => 1,
                    '⭐⭐ 2 - Mauvais' => 2,
                    '⭐⭐⭐ 3 - Moyen' => 3,
                    '⭐⭐⭐⭐ 4 - Bon' => 4,
                    '⭐⭐⭐⭐⭐ 5 - Excellent' => 5,
                ],
                'attr' => [
                    'class' => 'form-select',
                ]
            ])
            ->add('comments', TextareaType::class, [
                'label' => 'Commentaires de modération',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Réservé aux modérateurs'
                ]
            ])
        ;

        // Ajouter le champ de validation uniquement si c'est une modération
        if ($options['is_moderation']) {
            $builder->add('validated', CheckboxType::class, [
                'label' => '✅ Valider cet avis',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
            'is_moderation' => false,
        ]);
    }
}
