<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Form\TeamsType;
use App\Repository\TeamsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Knp\Component\Pager\PaginatorInterface;

class TeamsController extends AbstractController
{
    #[Route('/teams', name: 'teams_list')]
    public function teams_list(TeamsRepository $teamsRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // $teams = $teamsRepository->findAll();

        // return $this->render('teams/teams_list.html.twig', [
        //     'teams' => $teams,
        //     'title' => 'Listes des équipes'
        // ]);

        $queryBuilder = $teamsRepository->createQueryBuilder('a');

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            16 /*limit per page*/
        );

        return $this->render('teams/teams_list.html.twig', [
            'pagination' => $pagination,
            'title' => 'Listes avis'
        ]);
    }

    #[Route('/teams/{slug}-{id}', name: 'teams_details', requirements: [
        'slug' => '[a-z0-9-]+',
        'id' => '[0-9]+'
    ])]
    public function teams_details(int $id, TeamsRepository $teamsRepository): Response
    {
        $team = $teamsRepository->find($id);

        if (!$team) {
            throw $this->createNotFoundException('Equipe non trouvé');
        }

        return $this->render('teams/teams_details.html.twig', [
            'team' => $team,
            'title' => 'Team détails'
        ]);
    }

    #[Route('/teams/new', name: 'teams_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $team = new Teams();
        $form = $this->createForm(TeamsType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('pathImage')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = "/images/" . $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image : ' . $e->getMessage());
                    return $this->render('teams/new_teams.html.twig', [
                        'form' => $form->createView(),
                        'title' => 'Nouvelle équipe'
                    ]);
                }

                $team->setPathImage($newFilename);
            }
            $team->setCreatedAt(new \DateTimeImmutable());
            $asciiSlugger = new AsciiSlugger();
            $slug = $asciiSlugger->slug($form->get('name')->getData())->lower();
            $team->setSlug($slug);

            $entityManager->persist($team);
            $entityManager->flush();

            $this->addFlash('success', 'Équipe créée avec succès.');
            return $this->redirectToRoute('teams_list');
        }

        return $this->render('teams/new_teams.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouvelle équipe'
        ]);
    }

    #[Route('/teams/{slug}-{id}/edit', name: 'teams_edit', requirements: [
        'slug' => '[a-z0-9-]+',
        'id' => '[0-9]+'
    ])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        Teams $team
    ): Response {
        $form = $this->createForm(TeamsType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('pathImage')->getData();

            // Si une nouvelle image est téléchargée
            if ($imageFile) {
                // Supprimez l'ancienne image, s'il y en a une
                $oldFilename = $team->getPathImage();
                if ($oldFilename) {
                    // $oldFilepath = $this->getParameter('images_directory') . $oldFilename;
                    // Assurez-vous de ne pas dupliquer le préfixe '/images'
                    $oldFilepath = $this->getParameter('images_directory') . '/' . ltrim($oldFilename, '/images');
                    if (file_exists($oldFilepath)) {
                        // dd("rentre");
                        unlink($oldFilepath);
                    }
                    // dump($this->getParameter('images_directory'));
                    // dump($oldFilepath);
                    // dd("en dehort");
                }

                // Traitement de la nouvelle image
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = "/images/" . $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image : ' . $e->getMessage());
                    return $this->render('teams/edit_teams.html.twig', [
                        'form' => $form->createView(),
                        'title' => 'Modifier l\'équipe'
                    ]);
                }

                $team->setPathImage($newFilename);
            }

            $team->setUpdatedAt(new \DateTimeImmutable());
            $asciiSlugger = new AsciiSlugger();
            $slug = $asciiSlugger->slug($form->get('name')->getData())->lower();
            $team->setSlug($slug);

            $entityManager->flush();

            $this->addFlash('success', 'Équipe mise à jour avec succès.');
            return $this->redirectToRoute('teams_list');
        }

        return $this->render('teams/edit_teams.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier l\'équipe'
        ]);
    }

    #[Route('/teams/{slug}-{id}/delete', name: 'teams_delete', requirements: [
        'slug' => '[a-z0-9-]+',
        'id' => '\d+'
    ], methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Teams $team, EntityManagerInterface $entityManager): Response
    {
        // Vérifiez s'il y a un fichier d'image associé et supprimez-le
        $Filename = $team->getPathImage();
        if ($Filename) {
            $oldFilepath = $this->getParameter('images_directory') . '/' . ltrim($Filename, '/images');
            if (file_exists($oldFilepath)) {
                unlink($oldFilepath);
            }
        }

        $entityManager->remove($team);
        $entityManager->flush();

        return $this->redirectToRoute('teams_list');
    }
}
