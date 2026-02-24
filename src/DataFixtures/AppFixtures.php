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
        /** -------------------------
         * ETATS
         * ------------------------- */
        $etatsData = ['Créée', 'Ouverte', 'Clôturée', 'Activité en cours', 'Passée', 'Annulée'];
        $etats = [];

        foreach ($etatsData as $libelle) {
            $etat = new Etats();
            $etat->setLibelle($libelle);
            $manager->persist($etat);
            $etats[] = $etat;
        }

        /** -------------------------
         * SITES
         * ------------------------- */
        $site1 = new Sites();
        $site1->setNomSite('Nantes');
        $manager->persist($site1);

        $site2 = new Sites();
        $site2->setNomSite('Rennes');
        $manager->persist($site2);

        /** -------------------------
         * VILLES
         * ------------------------- */
        $ville = new Villes();
        $ville->setNomVille('Nantes');
        $ville->setCodePostal('44000');
        $manager->persist($ville);

        /** -------------------------
         * LIEUX
         * ------------------------- */
        $lieu = new Lieux();
        $lieu->setNomLieu('Parc de Procé');
        $lieu->setRue('Rue des Dervallières');
        $lieu->setLatitude(47.2184);
        $lieu->setLongitude(-1.5536);
        $lieu->setVilles($ville);
        $manager->persist($lieu);

        /** -------------------------
         * PARTICIPANTS
         * ------------------------- */
        $participants = [];

        for ($i = 1; $i <= 5; $i++) {
            $participant = new Participants();
            $participant->setEmail("user$i@mail.fr");
            $participant->setPseudo("user$i");
            $participant->setNom("Nom$i");
            $participant->setPrenom("Prenom$i");
            $participant->setTelephone("060000000$i");
            $participant->setRoles(['ROLE_USER']);
            $participant->setAdministrateur($i === 1);
            $participant->setActif(true);
            $participant->setSites($site1);

            $hashedPassword = $this->passwordHasher->hashPassword($participant, 'password');
            $participant->setPassword($hashedPassword);

            $manager->persist($participant);
            $participants[] = $participant;
        }

        /** -------------------------
         * SORTIES
         * ------------------------- */
        $sorties = [];

        for ($i = 1; $i <= 3; $i++) {
            $sortie = new Sorties();
            $sortie->setNom("Sortie $i");
            $sortie->setDateHeureDebut(new \DateTime('+'.$i.' days'));
            $sortie->setDuree(120);
            $sortie->setDateLimiteInscription(new \DateTime('+'.($i-1).' days'));
            $sortie->setNbInscriptionMax(10);
            $sortie->setInfosSortie("Description sortie $i");
            $sortie->setUrlPhoto(null);
            $sortie->setOrganisateur($participants[0]);
            $sortie->setEtats($etats[1]); // Ouverte
            $sortie->setLieux($lieu);

            $manager->persist($sortie);
            $sorties[] = $sortie;
        }

        /** -------------------------
         * INSCRIPTIONS
         * ------------------------- */
        foreach ($sorties as $sortie) {
            foreach ($participants as $participant) {
                $inscription = new Inscriptions();
                $inscription->setParticipant($participant);
                $inscription->setSortie($sortie);
                $inscription->setDateInscription(new \DateTime());

                $manager->persist($inscription);
            }
        }

        $manager->flush();
    }
}
