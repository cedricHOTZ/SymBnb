<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\CompteType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, EntityManagerInterface $entityManager, AppAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifiez si le champ picture est vide
            if (empty($user->getPicture())) {
                // Si oui, attribuez l'avatar par défaut
                $user->setPicture('/avatars/avatar_default.png');
            }
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            return $userAuthenticator->authenticateUser($user, $authenticator, $request);
            // do anything else you need here, like send an email


            // return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $form = $this->createForm(CompteType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Profile mis à jour');
        }


        return $this->render('registration/compte.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/maj-mot-de-passe', name: 'app_maj-mot-de-passe')]
    public function modificationMdp(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        // Récupération de l'utilisateur actuellement connecté
        $user = $this->getUser();
        // Création d'une instance de la classe PasswordUpdate qui contiendra les données du formulaire
        $passwordUpdate = new PasswordUpdate();
        // Création du formulaire en utilisant PasswordUpdateType et en associant l'instance PasswordUpdate
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        // Traitement du formulaire en fonction des données de la requête
        $form->handleRequest($request);
        // Vérification si le formulaire a été soumis et s'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération de l'ancien mot de passe et du nouveau mot de passe depuis le formulaire
            $newPassword = $passwordUpdate->getNewPassword();
            // Validation de l'ancien mot de passe
            if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                // Hachage du nouveau mot de passe
                $newEncodedPassword = $passwordHasher->hashPassword($user, $newPassword);
                // Mise à jour du mot de passe haché de l'utilisateur
                $user->setPassword($newEncodedPassword);
                // Persistance des modifications dans la base de données
                $entityManager->persist($user);
                $entityManager->flush();
                // Message de succès en cas de réussite
                $this->addFlash('success', 'Votre mot de passe a été modifié');
                return $this->redirectToRoute('app_home');
            } else {
                // Message d'erreur en cas d'ancien mot de passe incorrect
                $this->addFlash('error', 'L\'ancien mot de passe est incorrect');
            }
        }
        // Rendu du formulaire et des éventuels messages d'erreur
        return $this->render('registration/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
