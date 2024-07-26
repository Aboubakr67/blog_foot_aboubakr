<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
    public function dashboard(): Response
    {
        return $this->render('home/dashboard.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
