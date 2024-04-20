<?php

namespace App\Form;

use App\Entity\Commentaire;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('note', IntegerType::class, [
                'label' => 'Note sur 5',
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                    'step' => 1,
                    'placeholder' => "Veuillez indiquer votre note de 0 à 5"
                ]
            ])
            ->add('contenu',TextareaType::class, [
            'label' => 'Votre témoignage',
            'attr' => [
                'placeholder' => "N'hésitez pas à être précis, cela aidera nos futus voyageurs"
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
