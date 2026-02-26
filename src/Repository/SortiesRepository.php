<?php

namespace App\Repository;

use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sorties>
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }
    public function getSortiesHome(int $participantId,array $filtres = []): array
    {
        $query = $this->createQueryBuilder('sorties')
            ->select(
                'sorties.id',
                'sorties.nom',
                'sorties.dateHeureDebut',
                'sorties.dateLimiteInscription',
                'sorties.nbInscriptionMax',
                'etats.libelle AS etat',
                'sites.nomSite AS site',
                'organisateurParticipant.nom AS organisateur',
                'SUM(CASE WHEN inscriptions.isInscrit = true THEN 1 ELSE 0 END) AS nbInscrits',
                "CASE WHEN inscriptionParticipant.isInscrit = true THEN 'X' ELSE '' END AS inscrit"
            )

            // Tous les inscrits (pour le COUNT)
            ->leftJoin('sorties.inscriptions', 'inscriptions')

            // Vérifie si le participant courant est inscrit
            ->leftJoin(
                'sorties.inscriptions',
                'inscriptionParticipant',
                'WITH',
                'inscriptionParticipant.participant = :participantId'
            )

            ->leftJoin('sorties.etats', 'etats')
            ->leftJoin('sorties.organisateur', 'organisateurParticipant')
            ->leftJoin('organisateurParticipant.sites', 'sites')
            ->setParameter('participantId', $participantId)
            ->groupBy(
                'sorties.id',
                'etats.libelle',
                'organisateurParticipant.nom',
                'sites.nomSite',
                'inscriptionParticipant.id'
            );

        if (!empty($filtres['siteId'])) {
            $query->andWhere('sites.id = :siteId')
                ->setParameter('siteId', (int)$filtres['siteId']);

        }

        if (!empty($filtres['nomSortie'])) {
            $query->andWhere('sorties.nom LIKE :nomSortie')
                ->setParameter('nomSortie', '%' . $filtres['nomSortie'] . '%');
        }

//        if (!empty($filtres['dateDebut'])) {
//            $query->andWhere('sorties.dateHeureDebut >= :dateDebut')
//                ->setParameter('dateDebut', new \DateTime($filters['dateDebut']));
//        }
//
//        if (!empty($filtres['dateFin'])) {
//            $query->andWhere('sorties.dateHeureDebut <= :dateFin')
//                ->setParameter('dateFin', new \DateTime($filters['dateFin']));
//        }
//
//        if (!empty($filtres['etat'])) {
//            $query->andWhere('etats.libelle = :etat')
//                ->setParameter('etat', $filters['etat']);
//        }
            return $query->getQuery()->getArrayResult();
    }

//    /**
//     * @return Sorties[] Returns an array of Sorties objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sorties
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
