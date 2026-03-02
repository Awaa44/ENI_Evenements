<?php

namespace App\Controller;

use App\Entity\Participants;
use App\Form\ProfilType;
use App\Repository\ParticipantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $participant = $this->getUser();
        $form = $this->createForm(ProfilType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $participant->setPassword($userPasswordHasher->hashPassword($participant, $plainPassword));
            }
            $photoFile = $form->get('photo')->getData();
            if ($photoFile instanceof UploadedFile) {
                $newFileName = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFileName
                );
                $participant->setPhoto($newFileName);
            }

            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', "Votre compte à été modifiée avec succès !");

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/myprofile.html.twig', [
            'profilForm' => $form->createView(),
        ]);

    }

    #[Route('/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('app_home_index');
         }

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

    #[Route('/profile/{id}', name: 'app_profile_show')]
    public function showProfile(int $id, ParticipantsRepository $participantsRepository): Response
    {
        $participant = $participantsRepository->find($id);

        if (!$participant) {
            throw $this->createNotFoundException('Participant non trouvé !');
        }

        return $this->render('profile/show.html.twig', [
            'participant' => $participant,

        ]);
    }
}
