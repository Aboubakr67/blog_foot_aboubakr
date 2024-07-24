<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\Games;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $user = $this->security->getUser();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'attr' => ['class' => 'user-select'], // For potential custom JS or styling
            ]);
        } else {
            // $builder->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'username',
            //     'choices' => [$user],
            //     'disabled' => true,
            // ]);
            $builder->add('username', TextType::class, [
                'data' => $user->getUsername(),
                'disabled' => true,
                'mapped' => false, // This ensures that this field is not mapped to the entity directly
            ]);

            // HiddenType to store the user ID
            $builder->add('user', HiddenType::class, [
                'data' => $user->getId(),
                'mapped' => false, // Ensure this field is not directly mapped to the entity
            ]);
        }

        $builder
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'username',
            // ])
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
