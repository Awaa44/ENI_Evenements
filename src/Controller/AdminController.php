<?php

namespace App\Controller;

use App\Entity\Participants;
use App\Form\AdminCreateUserType;
use App\Repository\ParticipantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;


final class AdminController extends AbstractController
{
    #[Route('/admin/create-user', name: 'app_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function createUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $participant = new Participants();
        $form = $this->createForm(AdminCreateUserType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // chiffrer le mot de passe en clair
            $participant->setPassword($userPasswordHasher->hashPassword($participant, $plainPassword));

            if ($participant->isAdministrateur()) {
                $participant->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            } else {
                $participant->setRoles(['ROLE_USER']);
            }
            $participant->setActif(true);

            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', "Le compte de cet utilisateur à été créer avec succès !");

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/create-user.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/list-users', name: 'app_admin_list_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function listUsers(ParticipantsRepository $participantsRepository): Response
    {
        $participants = $participantsRepository->findAll();
        return $this->render('admin/list-users.html.twig', [
            'participants' => $participants,

        ]);
    }
}
