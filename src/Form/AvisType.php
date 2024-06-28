<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\Games;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('game', EntityType::class, [
                'class' => Games::class,
                'choice_label' => function (Games $game) {
                    return sprintf(
                        '%s - %s (%s)',
                        $game->getEquipeDomicile()->getName(),
                        $game->getEquipeExterieur()->getName(),
                        $game->getScore()
                    );
                },
            ])
            ->add('commentaire');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
