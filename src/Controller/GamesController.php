<?php

namespace App\Controller;

use App\Repository\GamesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GamesController extends AbstractController
{
    #[Route('/games', name: 'games_list')]
    public function games_list(GamesRepository $gamesRepository): Response
    {
        $games = $gamesRepository->findAll();

        return $this->render('games/games_list.html.twig', [
            'games' => $games,
            'title' => 'Listes des matchs'
        ]);
    }
}
