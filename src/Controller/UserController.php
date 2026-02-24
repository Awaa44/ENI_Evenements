<?php

namespace App\Controller;

use App\Entity\Participants;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class UserController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $participant = new Participants();
//      $participant->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $participant->setActif(true);
            $participant->setAdministrateur(false);

            // encode the plain password
            $participant->setPassword($userPasswordHasher->hashPassword($participant, $plainPassword));

            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', "Votre compte à été créée avec succès !");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('inscription/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('app_home');
        // }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


}
