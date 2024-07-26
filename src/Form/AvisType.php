<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\Games;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AvisType extends AbstractType
{
    private $security;
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $user = $this->security->getUser();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'attr' => ['class' => 'user-select'],
            ]);
        } else {
            $builder->add('username', TextType::class, [
                'data' => $user->getUsername(),
                'disabled' => true,
                'mapped' => false,
            ]);

            // HiddenType to store the user ID
            $builder->add('user', HiddenType::class, [
                'data' => $user->getId(),
                'mapped' => false,
            ]);
        }

        $builder
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
            ->add('commentaire')
            // ->addEventListener(
            //     FormEvents::PRE_SUBMIT,
            //     $this->generateSlug(...)
            // )
        ;
    }

    public function generateSlug(PreSubmitEvent $preSubmitEvent): void
    {
        $data = $preSubmitEvent->getData();
        $form = $preSubmitEvent->getForm();

        $gameId = $data['game'];
        $game = $this->entityManager->getRepository(Games::class)->find($gameId);

        if ($game) {
            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($game->getEquipeExterieur()->getName() . "-vs-" . $game->getEquipeDomicile()->getName())->lower();
            // dump($slug);

            $data['slug'] = $slug;
            $preSubmitEvent->setData($data);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
