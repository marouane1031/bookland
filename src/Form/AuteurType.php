<?php

namespace App\Form;

use App\Entity\Auteur;
use App\Entity\Livre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_prenom', TextType::class, [
                'label' => 'Nom et Prénom'
            ])
            ->add('sexe', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Masculin' => 'M',
                    'Feminin' => 'F'
                ],
                'multiple' => false
            ])
            ->add('date_de_naissance', DateType::class, [
                'label' => 'Date de naissance',
                'attr' => [
                    'class' => 'datepicker'
                ],
                'widget' => 'single_text',
                'html5' => false
            ])
            ->add('nationalite', TextType::class, ['label' => 'Nationalité'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Auteur::class,
        ]);
    }
}
