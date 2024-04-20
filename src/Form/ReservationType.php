<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => "single_text",
                'attr' => [
                    'placeholder' => 'Indiquez la date de votre arrivée'
                ]
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de départ',
                'widget' => "single_text",
                'attr' => [
                    'placeholder' => 'Indiquez la date de votre départ'
                ]
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Si vous avez un commentaire, n\'hésitez pas à en faire part'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
