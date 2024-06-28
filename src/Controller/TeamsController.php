<?php

namespace App\Controller;

use App\Repository\TeamsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamsController extends AbstractController
{
    #[Route('/teams', name: 'teams_list')]
    public function teams_list(TeamsRepository $teamsRepository): Response
    {
        $teams = $teamsRepository->findAll();

        return $this->render('teams/teams_list.html.twig', [
            'teams' => $teams,
            'title' => 'Listes des équipes'
        ]);
    }
}
