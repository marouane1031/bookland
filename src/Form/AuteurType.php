<?php

namespace App\Form;

use App\Entity\Auteur;
use App\Entity\Livre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_prenom', TextType::class)
            ->add('sexe')
            ->add('date_de_naissance', TextType::class, [
                'attr' => [
                    'class' => 'datepicker'
                ]
            ])
            ->add('nationalite', TextType::class)
            ->add('livres', EntityType::class, [
                'class' => Livre::class,
                'multiple' => true,
                'attr' => [
                    'class' => 'input-field'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Auteur::class,
        ]);
    }
}
