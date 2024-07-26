<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use App\Repository\GamesRepository;
use App\Repository\TeamsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    private $gamesRepository;

    public function __construct(GamesRepository $gamesRepository)
    {
        $this->gamesRepository = $gamesRepository;
    }

    #[Route('/api/games', name: 'api_games')]
    public function getGames(GamesRepository $gamesRepository): JsonResponse
    {
        $games = $gamesRepository->findAll();

        return $this->json($games, 200, [], ['groups' => 'game.list']);
    }

    #[Route('/api/avis', name: 'api_avis')]
    public function getAvis(AvisRepository $avisRepository): JsonResponse
    {
        $avis = $avisRepository->findAll();

        return $this->json($avis, 200, [], ['groups' => 'avis.list']);
    }

    #[Route('/api/teams', name: 'api_teams')]
    public function getTeams(TeamsRepository $teamsRepository): JsonResponse
    {
        $teams = $teamsRepository->findAll();

        return $this->json($teams, 200, [], ['groups' => 'team.list']);
    }
}