<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Games;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AvisController extends AbstractController
{
    // #[Route('/avis', name: 'avis_list')]
    // public function avis_list(AvisRepository $avisRepository): Response
    // {
    //     $avis = $avisRepository->findAll();

    //     return $this->render('avis/avis_list.html.twig', [
    //         'avis' => $avis,
    //         'title' => 'Listes avis'
    //     ]);
    // }

    #[Route('/avis', name: 'avis_list')]
    public function avis_list(AvisRepository $avisRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $avisRepository->createQueryBuilder('a');

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            9 /*limit per page*/
        );

        return $this->render('avis/avis_list.html.twig', [
            'pagination' => $pagination,
            'title' => 'Listes avis'
        ]);
    }


    #[Route('/avis/{slug}-{id}', name: 'avis_details', requirements: [
        'slug' => '[a-z0-9-]+',
        'id' => '[0-9]+'
    ])]
    public function avis_details(int $id, AvisRepository $avisRepository): Response
    {
        $avis = $avisRepository->find($id);

        if (!$avis) {
            throw $this->createNotFoundException('Avis non trouvé');
        }

        return $this->render('avis/avis_details.html.twig', [
            'avis' => $avis,
            'title' => 'Avis détails'
        ]);
    }


    #[Route('/avis/new', name: 'avis_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security, UserRepository $userRepository): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Get the current user
            $user = $security->getUser();

            // For non-admin users, set the user explicitly
            if (!$security->isGranted('ROLE_ADMIN')) {
                $userId = $form->get('user')->getData();
                $user = $userRepository->find($userId);
                if ($user) {
                    $avis->setUser($user);
                } else {
                    throw new \Exception('User not found');
                }
            }

            $avis->setCreatedAt(new \DateTimeImmutable());

            // Get the game from the form
            $game = $form->get('game')->getData();

            if ($game instanceof Games) {
                $slugger = new AsciiSlugger();
                $slug = $slugger->slug($game->getEquipeDomicile()->getName() . "-vs-" . $game->getEquipeExterieur()->getName())->lower();
                $avis->setSlug($slug);
            } else {
                throw new \Exception('Game not found');
            }

            $entityManager->persist($avis);
            $entityManager->flush();
            return $this->redirectToRoute('avis_list');
        }

        return $this->render('avis/new_avis.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouvelle avis'
        ]);
    }

    #[Route('/avis/{slug}-{id}/edit', name: 'avis_edit', requirements: [
        'slug' => '[a-z0-9-]+',
        'id' => '[0-9]+'
    ])]
    #[IsGranted('ROLE_USER')]
    public function edit(Avis $avis, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisType::class, $avis);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setUpdatedAt(new \DateTimeImmutable());

            // Get the game from the form
            $game = $form->get('game')->getData();

            if ($game instanceof Games) {
                $slugger = new AsciiSlugger();
                $slug = $slugger->slug($game->getEquipeDomicile()->getName() . "-vs-" . $game->getEquipeExterieur()->getName())->lower();
                $avis->setSlug($slug);
            } else {
                throw new \Exception('Game not found');
            }

            $entityManager->persist($avis);
            $entityManager->flush();

            return $this->redirectToRoute('avis_list');
        }

        return $this->render('avis/edit_avis.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier l\'avis',
        ]);
    }

    #[Route('/avis/{slug}-{id}/delete', name: 'avis_delete', requirements: [
        'slug' => '[a-z0-9-]+',
        'id' => '\d+'
    ], methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Avis $avis, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($avis);
        $entityManager->flush();

        return $this->redirectToRoute('avis_list');
    }
}
