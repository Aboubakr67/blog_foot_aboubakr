<?php

namespace App\Controller;

use App\Entity\Games;
use App\Form\GamesType;
use App\Repository\GamesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Knp\Component\Pager\PaginatorInterface;

class GamesController extends AbstractController
{
    #[Route('/games', name: 'games_list')]
    public function games_list(GamesRepository $gamesRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $gamesRepository->findAllSorted();

        $pagination = $paginator->paginate(
            $queryBuilder, 
            $request->query->getInt('page', 1), 
            9 
        );

        return $this->render('games/games_list.html.twig', [
            'pagination' => $pagination,
            'title' => 'Listes avis'
        ]);
    }


    #[Route('/games/{slug}-{id}', name: 'games_details', requirements: [
        'slug' => '[a-z0-9-]+',
        'id' => '[0-9]+'
    ])]
    public function games_details(int $id, GamesRepository $gamesRepository): Response
    {
        $game = $gamesRepository->find($id);

        if (!$game) {
            throw $this->createNotFoundException('Game non trouvé');
        }

        return $this->render('games/games_details.html.twig', [
            'game' => $game,
            'title' => 'Game détails'
        ]);
    }

    #[Route('/games/new', name: 'games_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Games();
        $form = $this->createForm(GamesType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $game->setCreatedAt(new \DateTimeImmutable());

            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($game->getEquipeDomicile()->getName() . "-vs-" . $game->getEquipeExterieur()->getName())->lower();
            $game->setSlug($slug);

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('games_list');
        }

        return $this->render('games/new_games.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouveau jeu'
        ]);
    }

    #[Route('/games/{slug}-{id}/edit', name: 'games_edit', requirements: [
        'slug' => '[a-z0-9-]+',
        'id' => '[0-9]+'
    ])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Games $game, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GamesType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $game->setUpdatedAt(new \DateTimeImmutable());

            
            $gameForm = $form->getData();

            if ($gameForm instanceof Games) {
                $slugger = new AsciiSlugger();
                $slug = $slugger->slug($gameForm->getEquipeDomicile()->getName() . "-vs-" . $game->getEquipeExterieur()->getName())->lower();
                $game->setSlug($slug);
            } else {
                throw new \Exception('Game not found');
            }

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('games_list');
        }

        return $this->render('games/edit_games.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier le jeu',
        ]);
    }

    // TODO marche pas car jointure ( à faire plus tard )
    // #[Route('/games/{slug}-{id}/delete', name: 'games_delete', requirements: [
    //     'slug' => '[a-z0-9-]+',
    //     'id' => '[0-9]+'
    // ], methods: ['DELETE'])]
    // #[IsGranted('ROLE_ADMIN')]
    // public function delete(Games $game, EntityManagerInterface $entityManager): Response
    // {
    //     $entityManager->remove($game);
    //     $entityManager->flush();

    //     return $this->redirectToRoute('games_list');
    // }
}
