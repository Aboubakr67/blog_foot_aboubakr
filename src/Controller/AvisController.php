<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AvisController extends AbstractController
{
    #[Route('/avis', name: 'avis_list')]
    public function avis_list(AvisRepository $avisRepository): Response
    {
        $avis = $avisRepository->findAll();

        return $this->render('avis/avis_list.html.twig', [
            'avis' => $avis,
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
            $entityManager->persist($avis);
            $entityManager->flush();
            return $this->redirectToRoute('avis_list');
        }

        return $this->render('avis/new_avis.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouvelle avis'
        ]);
    }

    #[Route('/avis/{slug}-vs-{id}/edit', name: 'avis_edit', requirements: [
        'slug' => '[a-z0-9-]+',
        'id' => '[0-9]+'
    ])]
    public function edit(Avis $avis, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisType::class, $avis);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            return $this->redirectToRoute('avis_list');
        }

        return $this->render('avis/edit_avis.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier l\'avis',
        ]);
    }

    #[Route('/avis/{id}/delete', name: 'avis_delete')]
    public function delete(Avis $avis, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($avis);
        $entityManager->flush();

        return $this->redirectToRoute('avis_list');
    }
}
