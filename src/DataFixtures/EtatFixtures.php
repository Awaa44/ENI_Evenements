<?php

namespace App\DataFixtures;

use App\Entity\Etats;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    public const ETAT_CREE = 'etat_cree';
    public const ETAT_OUVERTE = 'etat_ouverte';
    public const ETAT_CLOTUREE = 'etat_cloturee';
    public const ETAT_ACTIVITE_EN_COURS = 'etat_activite_en_cours';
    public const ETAT_PASSEE = 'etat_passee';
    public const ETAT_ANNULEE = 'etat_annulee';

    public function load(ObjectManager $manager): void
    {
        $etats = [
            self::ETAT_CREE => 'Créée',
            self::ETAT_OUVERTE => 'Ouverte',
            self::ETAT_CLOTUREE => 'Clôturée',
            self::ETAT_ACTIVITE_EN_COURS => 'Activité en cours',
            self::ETAT_PASSEE => 'Passée',
            self::ETAT_ANNULEE => 'Annulée',
        ];

        foreach ($etats as $reference => $libelle) {
            $etat = new Etats();
            $etat->setLibelle($libelle);

            $manager->persist($etat);
            $this->addReference($reference, $etat);
        }

        $manager->flush();
    }
}
