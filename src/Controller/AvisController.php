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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AvisController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/avis', name: 'avis_list')]
    public function avis_list(AvisRepository $avisRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $avisRepository->findAllSorted();

        $pagination = $paginator->paginate(
            $queryBuilder, 
            $request->query->getInt('page', 1), 
            9
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

            $user = $security->getUser();

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
        $user = $this->getUser();
        // Vérifiez si l'utilisateur est un administrateur ou s'il est le propriétaire de l'avis
        if (!$this->security->isGranted('ROLE_ADMIN') && $user->getId() !== $avis->getUser()->getId()) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à modifier cet avis.');
        }

        $form = $this->createForm(AvisType::class, $avis);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setUpdatedAt(new \DateTimeImmutable());

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

        $user = $this->getUser();

        if (!$this->security->isGranted('ROLE_ADMIN') && $user->getId() !== $avis->getUser()->getId()) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet avis.');
        }

        $entityManager->remove($avis);
        $entityManager->flush();

        return $this->redirectToRoute('avis_list');
    }
}
