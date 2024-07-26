<?php

namespace App\Controller;

use App\Repository\GamesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    private $gamesRepository;

    public function __construct(GamesRepository $gamesRepository)
    {
        $this->gamesRepository = $gamesRepository;
    }

    #[Route('/api/games', name: 'api_games')]
    public function getGames()
    {
        $games = $this->gamesRepository->findAll();
        $data = [];

        foreach ($games as $game) {
            $gameData = [
                'id' => $game->getId(),
                'title' => $game->getTitle(),
                'equipeDomicile' => [
                    'id' => $game->getEquipeDomicile()->getId(),
                    'name' => $game->getEquipeDomicile()->getName(),
                ],
                'equipeExterieur' => [
                    'id' => $game->getEquipeExterieur()->getId(),
                    'name' => $game->getEquipeExterieur()->getName(),
                ],
                'dateMatch' => $game->getDateMatch()->format('Y-m-d'),
                'createdAt' => $game->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $game->getUpdatedAt() ? $game->getUpdatedAt()->format('Y-m-d H:i:s') : null,
                'score' => $game->getScore(),
                'slug' => $game->getSlug(),
                'avis' => [],
            ];

            foreach ($game->getCommentaire() as $avis) {
                $gameData['avis'][] = [
                    'id' => $avis->getId(),
                    'user' => $avis->getUser()->getId(), // Assurez-vous d'ajouter plus de détails utilisateur si nécessaire
                    'commentaire' => $avis->getCommentaire(),
                    'createdAt' => $avis->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $avis->getUpdatedAt() ? $avis->getUpdatedAt()->format('Y-m-d H:i:s') : null,
                    'slug' => $avis->getSlug(),
                ];
            }

            $data[] = $gameData;
        }

        return new JsonResponse($data);
    }
    // public function getGames()
    // {
    //     $games = $this->gamesRepository->findAll();
    //     $data = [];

    //     foreach ($games as $game) {
    //         $data[] = [
    //             'id' => $game->getId(),
    //             'slug' => $game->getSlug(),
    //         ];
    //     }

    //     return new JsonResponse($data);
    // }
}