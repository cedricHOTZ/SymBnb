<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjouterAnnonceType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title' ,TextType::class,$this->getConfiguration("Titre","Tapez un super titre"))
           
            ->add('slug',TextType::class,$this->getConfiguration("Adresse web","Tapez l'adresse web",[
                'required' => false
            ]))
            ->add('coverImage',UrlType::class,$this->getConfiguration("URL de l'image","Donnez l'adresse d'une image"))
            ->add('introduction',TextType::class,$this->getConfiguration("Introduction","Donnez une introduction globale"))
            ->add('content',TextareaType::class,$this->getConfiguration("Déscription détaillée","Tapez une description qui donne envie de venir chez vous"))
            ->add('rooms',IntegerType::class,$this->getConfiguration("Nombre de chambre","Le nombre de chambres disponibles"))
            ->add('price',MoneyType::class,$this->getConfiguration("Prix par nuit","Indiquez le prix que vous voulez pour une nuit"))
            //ajouter plusieurs images du champ
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ]
            )
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
