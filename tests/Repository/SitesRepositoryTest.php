<?php

namespace App\Tests\Repository;

use App\Repository\SitesRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SitesRepositoryTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
    }

    public function testFindBySite(): void {

        $sitesRepository = static::getContainer()->get(SitesRepository::class);

        $siteNom = 'Site Nantes';
        $sites = $sitesRepository->findBySite($siteNom);

        //vérifie que le nom tapé est bien dans la base
        foreach ($sites as $site) {
            $this->assertStringContainsStringIgnoringCase($siteNom, $site->getNomSite());
        }
    }

    public function testFindBySitePartiel(): void {

        $sitesRepository = static::getContainer()->get(SitesRepository::class);

        $siteNom = 'nan';
        $sites = $sitesRepository->findBySite($siteNom);

        //vérifie que le nom partiel tapé est bien dans la base
        foreach ($sites as $site) {
            $this->assertStringContainsStringIgnoringCase($siteNom, $site->getNomSite());
        }
    }

    public function testFindBySiteBlank(): void {

        $sitesRepository = static::getContainer()->get(SitesRepository::class);

        $siteNom = 'site_inexistant';
        $sites = $sitesRepository->findBySite($siteNom);

        //vérifie que la base ne retourne rien
        $this->assertEmpty($sites);
    }
}
