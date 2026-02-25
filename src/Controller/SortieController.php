<?php

namespace App\Controller;

use App\Entity\Etats;
use App\Entity\Lieux;
use App\Entity\Sorties;
use App\Entity\Villes;
use App\Form\SortieType;
use App\Repository\EtatsRepository;
use App\Repository\LieuxRepository;
use App\Repository\ParticipantsRepository;
use App\Repository\SortiesRepository;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie', name: 'app_sortie')]
final class SortieController extends AbstractController
{
    #[Route('/{id}', name: '_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function afficherDetail(SortiesRepository $sortieRepository, int $id): Response
    {
        $sortie = $sortieRepository->find($id);

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/create', name: '_create')]
    public function createSortie(Request $request, EntityManagerInterface $em,
                                 ParticipantsRepository $participantRepository,
    EtatsRepository $etatsRepository): Response
    {
        $sortie = new Sorties();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        //test à enlever
        $participant = $participantRepository->find(1);
        $sortie->setOrganisateur($participant);

        //enregistrement de l'organisateur par défaut avec la personne connectée
        //$sortie->setOrganisateur($this->getUser());

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if ($request->request->get('creer')) {
                //mettre l'état créée par défaut
                $etat = $etatsRepository->find(1);
                $sortie->setEtats($etat);
                $sortie->setEtatSortie(1);
                $message = 'Votre sortie a été créée';
            } else {
                $etat = $etatsRepository->find(2);
                $sortie->setEtats($etat);
                $sortie->setEtatSortie(2);
                $message = 'Votre sortie a été publiée';
            }

            $em->persist($sortie);
            $em->flush();

            //message de succès
            $this->addFlash('success', $message);
            return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie_form' => $sortieForm,
            'sortie' => $sortie,
            'isEdit' => false,
        ]);
    }

    //route Ajax pour récupérer les informations du lieu et les afficher dans la page create
    #[Route('/lieu/{id}', name: '_lieu_info')]
    public function getLieuInfo(Lieux $lieu): JsonResponse
    {
        return $this->json([
            'rue' => $lieu->getRue(),
            'codePostal' => $lieu->getVilles()->getCodePostal(),
            'ville' => $lieu->getVilles()->getNomVille(),
        ]);
    }

    #[Route('/ville/{id}', name: '_ville_info')]
    public function getVilleInfo(Villes $ville, LieuxRepository $lieuxRepository): JsonResponse
    {
        $lieux = $lieuxRepository->findBy(['villes' => $ville]);
        $listLieux = [];
        foreach ($lieux as $lieu) {
            $listLieux[] = [
                'idLieux' => $lieu->getId(),
                'nomLieux' => $lieu->getNomLieu(),
            ];
        }
        return $this->json($listLieux);
    }


    #[Route('/update/{id}', name: '_update', requirements: ['id'=> '\d+'])]
    public function updateSortie(Request $request, EntityManagerInterface $em, Sorties $sortie): Response
    {
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $em->flush();

            //message de succès
            $message = 'Votre sortie a été mise à jour';
            $this->addFlash('success', $message);
            return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie_form' => $sortieForm,
            'sortie' => $sortie,
            'isEdit' => true,
        ]);
    }




}
