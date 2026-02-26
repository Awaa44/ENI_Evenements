<?php

namespace App\DataFixtures;

use App\Entity\Etats;
use App\Entity\Inscriptions;
use App\Entity\Lieux;
use App\Entity\Participants;
use App\Entity\Sites;
use App\Entity\Sorties;
use App\Entity\Villes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        /* =========================
         * ETATS
         * ========================= */
        $etatsLabels = ['Créée', 'Ouverte', 'Clôturée', 'Activité en cours', 'Passée', 'Annulée'];
        $etats = [];

        foreach ($etatsLabels as $label) {
            $etat = new Etats();
            $etat->setLibelle($label);
            $manager->persist($etat);
            $etats[] = $etat;
        }

        /* =========================
         * VILLES
         * ========================= */
        $villesData = [
            ['Paris', '75000'],
            ['Nantes', '44000'],
            ['Rennes', '35000'],
            ['Lyon', '69000'],
            ['Bordeaux', '33000']
        ];

        $villes = [];

        foreach ($villesData as [$nom, $cp]) {
            $ville = new Villes();
            $ville->setNomVille($nom);
            $ville->setCodePostal($cp);
            $manager->persist($ville);
            $villes[] = $ville;
        }

        /* =========================
         * SITES
         * ========================= */
        $sites = [];

        foreach ($villes as $ville) {
            $site = new Sites();
            $site->setNomSite('Site ' . $ville->getNomVille());
            $manager->persist($site);
            $sites[] = $site;
        }

        /* =========================
         * LIEUX
         * ========================= */
        $lieux = [];

        foreach ($villes as $ville) {
            for ($i = 1; $i <= 2; $i++) {
                $lieu = new Lieux();
                $lieu->setNomLieu("Lieu $i - " . $ville->getNomVille());
                $lieu->setRue("Rue Exemple $i");
                $lieu->setLatitude(mt_rand(40, 50) + mt_rand() / mt_getrandmax());
                $lieu->setLongitude(mt_rand(-5, 5) + mt_rand() / mt_getrandmax());
                $lieu->setVilles($ville);

                $manager->persist($lieu);
                $lieux[] = $lieu;
            }
        }

        /* =========================
         * PARTICIPANTS (REALISTES)
         * ========================= */

        $noms = ['Martin','Bernard','Dubois','Thomas','Robert','Petit','Durand','Leroy'];
        $prenoms = ['Jean','Paul','Marie','Luc','Emma','Sophie','Lucas','Julie'];

        $participants = [];

        for ($i = 1; $i <= 20; $i++) {

            $participant = new Participants();
            $nom = $noms[array_rand($noms)];
            $prenom = $prenoms[array_rand($prenoms)];

            $participant->setEmail(strtolower($prenom.'.'.$nom.$i.'@mail.fr'));
            $participant->setPseudo(strtolower($prenom.$i));
            $participant->setNom($nom);
            $participant->setPrenom($prenom);
            $participant->setTelephone('06'.rand(10000000,99999999));
            $participant->setRoles($i === 1 ? ['ROLE_ADMIN'] : ['ROLE_USER']);
            $participant->setAdministrateur($i === 1);
            $participant->setActif(true);
            $participant->setSites($sites[array_rand($sites)]);

            $participant->setPassword(
                $this->passwordHasher->hashPassword($participant, 'password')
            );

            $manager->persist($participant);
            $participants[] = $participant;
        }

        /* =========================
         * SORTIES REALISTES
         * ========================= */

        $themes = ['Randonnée', 'Afterwork', 'Conférence', 'Tournoi Sportif', 'Visite Culturelle', 'Apéro Réseau'];

        $sorties = [];

        for ($i = 1; $i <= 15; $i++) {

            $sortie = new Sorties();
            $sortie->setNom($themes[array_rand($themes)] . " #$i");
            $sortie->setDateHeureDebut((new \DateTime())->modify('+' . rand(1,30) . ' days'));
            $sortie->setDuree(rand(60,240));
            $sortie->setDateLimiteInscription((new \DateTime())->modify('+' . rand(0,10) . ' days'));
            $sortie->setNbInscriptionMax(rand(5,30));
            $sortie->setInfosSortie("Sortie organisée autour du thème " . $sortie->getNom());
            $sortie->setUrlPhoto(null);
            $sortie->setOrganisateur($participants[array_rand($participants)]);
            $sortie->setEtats($etats[array_rand($etats)]);
            $sortie->setLieux($lieux[array_rand($lieux)]);

            $manager->persist($sortie);
            $sorties[] = $sortie;
        }

        /* =========================
         * INSCRIPTIONS ALEATOIRES
         * ========================= */

        foreach ($sorties as $sortie) {

            $nbParticipants = rand(3, 10);
            $participantsShuffle = $participants;
            shuffle($participantsShuffle);

            for ($i = 0; $i < $nbParticipants; $i++) {

                if (!isset($participantsShuffle[$i])) {
                    break;
                }

                $inscription = new Inscriptions();
                $inscription->setParticipant($participantsShuffle[$i]);
                $inscription->setSortie($sortie);
                $inscription->setDateInscription(new \DateTime());
                // ✅ Génération aléatoire du boolean isInscrit
                $inscription->setIsInscrit((bool) random_int(0, 1));

                $manager->persist($inscription);
            }
        }

        $manager->flush();
    }
}
