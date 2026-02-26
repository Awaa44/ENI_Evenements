<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Participants;
use App\Entity\Sorties;
use App\Repository\InscriptionsRepository;
use App\Repository\LieuxRepository;
use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/home', name: 'app_home')]
final class HomeController extends AbstractController
{
    #[Route('/index', name: '_index')]
    public function index(SitesRepository $sitesRepository, SortiesRepository $sortieRepository, Request $request): Response
    {
        //Liste des sites
        $sites = $sitesRepository->findAll();
        $user = $this->getUser();
        if (!$user)
        {
            throw new \Exception("Utilisateur introuvable");
        }
        $idParticipant = $user->getId();
        //Tableau
        $tableau = $sortieRepository->getSortiesHome($idParticipant);
        dd($tableau);

        // Mise en session  de la requete
//        $request->getSession()->set('tableau', $tableau);

        return $this->render('home/index.html.twig', [
            'sites' => $sites,
            'tableau' => $tableau,
        ]);
    }
    #[Route('/desister/{idSortie}', name: '_desister', requirements: ['idSortie' => '\d+'], methods: ['GET'])]
    public function desister(
        InscriptionsRepository $inscriptionsRepository,
        SortiesRepository $sortiesRepository,
        EntityManagerInterface $em,
        int $idSortie
    ): Response
    {
        $result = $inscriptionsRepository->updateInscription($idSortie,false);
        $sortie = $em->find(Sorties::class, $idSortie);
        if (!$sortie)  {
            throw new \Exception("Sortie introuvable");
        }
        else
        {
            if ($result) {
                $this->addFlash('success','Vous êtes désinscrit à la sortie' . $sortie->getNom());
            }
        }
        return $this->redirectToRoute('app_home_index');
    }

    #[Route('/inscrire/{idSortie}', name: '_inscrire', requirements: ['idSortie' => '\d+'], methods: ['GET'])]
    public function inscrire(
        InscriptionsRepository $inscriptionsRepository,
        int $idSortie,
        EntityManagerInterface $em
    ): Response
    {
        //Mettre récupération idUser connecté
        $user = $this->getUser();
        if (!$user)
        {
            throw new \Exception("Utilisateur introuvable");
        }
        $idParticipant = $user->getId();

        // Récupération des entités liées
        $sortie = $em->find(Sorties::class, $idSortie);
        $participant = $em->find(Participants::class, $idParticipant);

        if (!$sortie || !$participant) {
            throw new \Exception("Sortie ou participant introuvable");
        }
        $existe = $inscriptionsRepository->existsByParticipantAndSortie($idParticipant, $idSortie);
        if ($existe) {
            $inscriptionsRepository->updateInscription($idSortie,true);
        } else {
            // Création de l’inscription
            $inscription = new Inscriptions();
            $inscription->setDateInscription(new \DateTime());
            $inscription->setIsInscrit(true);
            $inscription->setSortie($sortie);
            $inscription->setParticipant($participant);
            $em->persist($inscription);
            $em->flush();
        }
        $this->addFlash('success', 'Vous êtes inscrit à la sortie ' . $sortie->getNom());

        return $this->redirectToRoute('app_home_index');
    }

    #[Route('/filtrer', name: 'filtrer')]
    public function filtrer(Request $request, SortiesRepository $sortiesRepository): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            throw new \Exception("Utilisateur introuvable");
        }
        $idParticipant = $user->getId();

        $filtres = [
            'siteId'     => $request->query->get('idSite'),
            'nomSortie'  => $request->query->get('nomSortie'),
            'dateDebut'  => $request->query->get('dateDebut'),
            'dateFin'    => $request->query->get('dateFin'),
            'organisateur'   => $request->query->get('organisateur'),
            'inscrit'   => $request->query->get('inscrit'),
            'nonInscrit'   => $request->query->get('nonInscrit'),
            'passees'   => $request->query->get('passees'),
        ];
        $tableau = $sortiesRepository->getSortiesHome($idParticipant, $filtres);
        //dd($tableau);

        return $this->render('home/_tbody.html.twig', [
            'tableau' => $tableau
        ]);
    }
}
