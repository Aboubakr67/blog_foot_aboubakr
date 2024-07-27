<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use App\Repository\UserRepository;
use App\Repository\GamesRepository;
use App\Repository\TeamsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'title' => 'Accueil',
        ]);
    }

    #[Route('/mentions_legales', name: 'mentions_legales')]
    public function mentions_legales(): Response
    {
        return $this->render('mentions_legales/mentions_legales.html.twig');
    }

    #[Route('/dashboard', name: 'dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(
        AvisRepository $avisRepository,
        GamesRepository $gamesRepository,
        TeamsRepository $teamsRepository,
        UserRepository $userRepository
    ): Response {
    
        $countAvis = $avisRepository->count();
        $countTeams = $teamsRepository->count();
        $countGames = $gamesRepository->count();
        $countUsers = $userRepository->count();

        $reviewsPerGame = $avisRepository->countAvisPerGame();

        return $this->render('home/dashboard.html.twig', [
                    'countAvis' => $countAvis,
                    'countTeams' => $countTeams,
                    'countGames' => $countGames,
                    'countUsers' => $countUsers,
                    'reviewsPerGame' => $reviewsPerGame,
                ]);
       
    }
}
