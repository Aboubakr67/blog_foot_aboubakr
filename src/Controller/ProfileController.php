<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function viewProfile(): Response
    {
        $user = $this->getUser();

        return $this->render('profile/view.html.twig', [
            'user' => $user,
        ]);
    }

    // #[Route('/profile/edit', name: 'app_profile_edit')]
    // #[IsGranted('ROLE_USER')]
    // public function editProfile(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    // {
    //     $user = $this->getUser();
    //     $form = $this->createForm(ProfileFormType::class, $user);
        
    //     $form->handleRequest($request);
        
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         dd("aaa");
    //         // Traiter le mot de passe
    //         $plainPassword = $form->get('plainPassword')->getData();

    //         if ($plainPassword) {
    //             $user->setPassword(
    //                 $passwordHasher->hashPassword(
    //                     $user,
    //                     $plainPassword
    //                 )
    //             );
    //         }


    //         // Validation de l'utilisateur
    //         $errors = $validator->validate($user);
    //         if (count($errors) > 0) {
    //             foreach ($errors as $error) {
    //                 $this->addFlash('error', $error->getMessage());
    //                 dump($error->getMessage());
    //             }

    //             dd($errors);
    //             return $this->render('profile/edit.html.twig', [
    //                 'profileForm' => $form->createView(),
    //             ]);
    //         }

    //         $user->setEmail($form->get('email')->getData());
    //         $user->setUsername($form->get('username')->getData());

    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         $this->addFlash('success', 'Votre profil a été mis à jour.');

    //         return $this->redirectToRoute('app_profile');
    //     }

    //     return $this->render('profile/edit.html.twig', [
    //         'profileForm' => $form->createView(),
    //     ]);
    // }
}
