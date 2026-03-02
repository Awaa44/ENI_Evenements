<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Entity\Participants;
use App\Form\AdminCreateUserType;
use App\Form\ImportCsvType;
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

    #[Route('/admin/import-csv', name: 'app_admin_import_csv')]
    #[IsGranted('ROLE_ADMIN')]
    public function importCSV(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // On crée le formulaire avec juste le champ fichier CSV
        $form = $this->createForm(ImportCsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère le fichier uploadé
            $csvFile = $form->get('csvFile')->getData();

            if ($csvFile) {
                // On lit le fichier et on découpe chaque ligne aux virgules
                $csvData = array_map('str_getcsv', file($csvFile->getPathname()));
                // On récupère et enlève la première ligne (les titres)
                $header = array_shift($csvData);

                // On boucle sur chaque ligne de données
                foreach ($csvData as $row) {
                    // On associe les titres avec les valeurs -> $data['email'], $data['pseudo']...
                    $data = array_combine($header, $row);

                    // On cherche le site en base de données par son nom
                    $site = $entityManager->getRepository(Sites::class)->findOneBy(['nomSite' => $data['site']]);

                    // Si le site n'existe pas, on affiche une erreur et on passe à la ligne suivante
                    if (!$site) {
                        $this->addFlash('danger', "Le site " . $data['site'] . " n'existe pas.");
                        continue;
                    }

                    $participant = new Participants();
                    $participant->setEmail($data['email']);
                    $participant->setPseudo($data['pseudo']);
                    $participant->setNom($data['nom']);
                    $participant->setPrenom($data['prenom']);
                    $participant->setTelephone($data['telephone'] ?? null);
                    $participant->setSites($site);
                    $participant->setRoles(['ROLE_USER']);
                    $participant->setActif(true);
                    $participant->setAdministrateur(false);
                    $participant->setPassword(
                        $userPasswordHasher->hashPassword($participant, $data['password'])
                    );

                    $entityManager->persist($participant);
                }
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateurs importés avec succès !');
                return $this->redirectToRoute('app_admin_import_csv');
            }

        }
        return $this->render('admin/import-csv.html.twig', [
            'importForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/deactivate_user/{id}', name: 'app_admin_deactivate_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function deactivateUser(Participants $participant, EntityManagerInterface $entityManager): Response
    {

        $participant->setActif(false);

        $entityManager->persist($participant);
        $entityManager->flush();

        $this->addFlash('success', "Le compte de cet utilisateur à été déactivée avec succès !");

        return $this->redirectToRoute('app_admin_list_users');
    }

    #[Route('/admin/activate_user/{id}', name: 'app_admin_activate_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function activateUser(Participants $participant, EntityManagerInterface $entityManager): Response
    {

        $participant->setActif(true);

        $entityManager->persist($participant);
        $entityManager->flush();

        $this->addFlash('success', "Le compte de cet utilisateur à été activée avec succès !");

        return $this->redirectToRoute('app_admin_list_users');
    }

    #[Route('/admin/delete_user/{id}', name: 'app_admin_delete_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(Participants $participant, EntityManagerInterface $entityManager): Response
    {

        $entityManager->remove($participant);
        $entityManager->flush();

        $this->addFlash('success', "Le compte de cet utilisateur à été supprimé avec succès !");

        return $this->redirectToRoute('app_admin_list_users');
    }

}
