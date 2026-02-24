<?php

namespace App\Controller;

use App\Repository\LieuxRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(SiteRepository $sitesRepository,SortieRepository $sortieRepository): Response
    {
        $sites = $sitesRepository->findAll();
        $tableau = $sortieRepository->getSortiesHome(2);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sites' => $sites,
            'tableau' => $tableau,
        ]);
    }
}
