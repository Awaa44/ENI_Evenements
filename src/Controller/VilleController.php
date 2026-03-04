<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Entity\Sorties;
use App\Entity\Villes;
use App\Repository\LieuxRepository;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ville', name: 'app_ville')]
#[IsGranted('ROLE_ADMIN')]
final class VilleController extends AbstractController
{
    #[Route('/index', name: '_index')]
    public function index(VillesRepository $villesRepository): Response
    {
        $villes = $villesRepository->findAll();

        return $this->render('ville/index.html.twig', [
            'villes' => $villes,
        ]);
    }
    #[Route('/filtrer', name: 'filtrer')]
    public function filtrer(Request $request, VillesRepository $villesRepository): Response
    {
        $villeSaisie = $request->query->get('nom');
        $villes = $villesRepository->findNameVille($villeSaisie);

        return $this->render('ville/_tbody.html.twig', [
            'villes' => $villes
        ]);
    }
    #[Route('/modifier/{id}', name: 'ville_modifier', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function modifier(
        Request $request,
        Villes $ville,
        EntityManagerInterface $em
    ): Response {

        $ville->setNomVille($request->request->get('nom'));
        $ville->setCodePostal($request->request->get('codePostal'));

        $em->flush();

        return $this->redirectToRoute('app_ville_index');
    }
    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(
        Villes $villes,
        VillesRepository $villesRepository,
        Request $request
    ): Response
    {
        $token = $request->query->get('token');

        if (!$this->isCsrfTokenValid('ville_delete' . $villes->getId(), $token)) {
            $this->addFlash('danger', 'Action invalide.');
            return $this->redirectToRoute('app_ville_index');
        }

        if (!$villesRepository->canBeDeleted($villes)) {
            $this->addFlash('danger', 'Impossible de supprimer cette ville : '.$villes->getNomVille().' car un ou plusieurs lieux sont utilisés dans des sorties.');
            return $this->redirectToRoute('app_ville_index');
        }

        $villesRepository->deleteVilleWithLieux($villes);

        $this->addFlash('success', 'Ville : '.$villes->getNomVille().' et lieux associés supprimés.');
        return $this->redirectToRoute('app_ville_index');
    }
    #[Route('/ajouter', name: '_add', methods: ['POST'])]
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        $nom = trim($request->request->get('nom'));
        $codePostal = trim($request->request->get('codePostal'));

        if (empty($nom) || empty($codePostal)) {
            return new Response("Champs obligatoires", 400);
        }

        if (!preg_match('/^[0-9]{5}$/', $codePostal)) {
            return new Response("Code postal invalide", 400);
        }

        $ville = new Villes();
        $ville->setNomVille($nom);
        $ville->setCodePostal($codePostal);

        $em->persist($ville);
        $em->flush();

        $this->addFlash('success', 'La ville : '.$nom.' a été ajouté avec succès');
        // On renvoie le tableau mis à jour
        $villes = $em->getRepository(Villes::class)->findAll();

        return $this->render('ville/_tbody.html.twig', [
            'villes' => $villes
        ]);
    }
}
