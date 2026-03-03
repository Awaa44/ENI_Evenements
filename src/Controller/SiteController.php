<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Form\SiteType;
use App\Repository\ParticipantsRepository;
use App\Repository\SitesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function PHPUnit\Framework\isEmpty;

#[IsGranted("ROLE_ADMIN")]
#[Route('/site', name: 'app_site')]
final class SiteController extends AbstractController
{
    #[Route('/detail', name: '_detail', methods: ['GET','POST'])]
    public function detail(Request $request, SitesRepository $sitesRepository,
                           ParticipantsRepository $participantsRepository, EntityManagerInterface $em): Response
    {
        //récupérer la liste des sites par ordre alphabétique
        $sites = $sitesRepository->findBy([], ['nomSite' => 'ASC']);

        //créer un site
        $site = new Sites();
        $siteForm = $this->createForm(SiteType::class, $site);
        $siteForm->handleRequest($request);

        if($siteForm->isSubmitted() && $siteForm->isValid()) {
            if ($request->request->get('create')) {
                $em->persist($site);
                $em->flush();

                $message = "{$site->getNomSite()} a été ajouté avec succès";
                $this->addFlash('success', $message);
                return $this->redirectToRoute('app_site_detail');
            }
        }

        return $this->render('site/edit.html.twig', [
            'site' => $site,
            'site_form' => $siteForm,
            'sites' => $sites,
        ]);
    }

    #[Route('/update/{id}', name: '_update', requirements: ['id'=> '\d+'], methods: ['POST'])]
    public function updateSite(Request $request, EntityManagerInterface $em, Sites $site): Response
    {
        $nomSite = $request->request->get('nomSite');

        if ($request->request->get('update')) {

            $site->setNomSite($nomSite);
            $em->persist($site);
            $em->flush();

            $this->addFlash('success', 'Le site a été modifié');
            return $this->redirectToRoute('app_site_detail');
        }

        $this->addFlash('danger', 'Impossible de modifier le site');
        return $this->redirectToRoute('app_site_detail');
    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id'=> '\d+'])]
    public function deleteSite(Request $request, Sites $site,
                               EntityManagerInterface $em): Response
    {
        $token = $request->query->get('_token');

        if($this->isCsrfTokenValid('delete'.$site->getId(), $token)) {

            //vérifier si participant lié au site
            if(!$site->getParticipants()->isEmpty()){
                $this->addFlash('danger', 'Impossible de supprimer le site tant que des participants y sont liés');
                return $this->redirectToRoute('app_site_detail');
            }

            $em->remove($site);
            $em->flush();

            $message = 'Votre site a été supprimé';
            $this->addFlash('success', $message);
            return $this->redirectToRoute('app_site_detail');

        }

        $message = 'Impossible de supprimer le site';
        $this->addFlash('danger', $message);
        return $this->redirectToRoute('app_site_detail');
    }

    #[Route('/filter', name: '_filter', methods: ['GET'])]
    public function filterSite(Request $request, SitesRepository $sitesRepository): Response
    {
        $siteSaisie = $request->query->get('nomSite');
        $site = $sitesRepository->findBySite($siteSaisie);

        return $this->render('site/detail.html.twig', [
            'site' => $site,
        ]);
    }
}
