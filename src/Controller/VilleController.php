<?php

namespace App\Controller;

use App\Entity\Villes;
use App\Repository\LieuxRepository;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/ville', name: 'app_ville')]
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
        if (!$villes) {
            throw new \Exception("Saisie de la ville incorrecte");
        }

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
        EntityManagerInterface $em,
        Request $request,
        LieuxRepository $lieuxRepository
    ): Response
    {
        $token = $request->query->get('token');
        if ($this->isCsrfTokenValid('ville_delete' . $villes->getId(), $token))
        {
            $lieux = $lieuxRepository->find($villes->getId());
            foreach ($lieux->g() as $lieu) {
                $em->remove($lieu);
            }
            $em->remove($villes);
            $em->flush();

            $this->addFlash('success', 'La ville'.$villes->getNomVille().'a été supprimée');
            return $this->redirectToRoute('app_ville_index');
        }
        $this->addFlash('danger', 'Cette action est impossible!');
        return $this->redirectToRoute('app_ville_index');
    }
}
