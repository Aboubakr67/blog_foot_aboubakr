<?php

namespace App\Form;

use App\Entity\Games;
use App\Entity\Teams;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GamesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('equipeDomicile', EntityType::class, [
                'class' => Teams::class,
                'choice_label' => 'name',
            ])
            ->add('equipeExterieur', EntityType::class, [
                'class' => Teams::class,
                'choice_label' => 'name',
            ])
            ->add('dateMatch')
            ->add('score', TextType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Games::class,
        ]);
    }
}
