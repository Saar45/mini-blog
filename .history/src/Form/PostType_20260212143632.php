<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Le titre ne peut pas être vide']),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['class' => 'form-control', 'rows' => 10],
                'constraints' => [
                    new NotBlank(['message' => 'Le contenu ne peut pas être vide']),
                ],
            ])
            ->add('picture', TextType::class, [
                'label' => 'URL de l\'image',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'https://example.com/image.jpg'],
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une catégorie']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
