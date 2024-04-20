<?php

namespace App\Form;

use App\Entity\Annonces;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre de l\'annonce'
                ]
            ])
            ->add('slug', TextType::class, [
                'label' => 'Url',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Adresse web'
                ]
            ])
            ->add('coverImage', UrlType::class, [
                'label' => 'Image',
                'attr' => [
                    'placeholder' => 'adresse de l\'image'
                ]
            ])

            ->add('introduction', TextType::class, [
                'label' => 'Introduction',
                'attr' => [
                    'placeholder' => 'Introduction de l\'annonce'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description de l\'annonce'
                ]
            ])

            ->add('rooms', IntegerType::class, [
                'label' => 'Nombre de chambre',
                'attr' => [
                    'placeholder' => 'Nombre de chambres disponibles'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'attr' => [
                    'placeholder' => 'Indiquez le prix de la nuit'
                ]
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}
