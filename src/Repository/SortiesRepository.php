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
    public function getSortiesHome(int $participantId): array
    {
        return $this->createQueryBuilder('sorties')
            ->select(
                'sorties.id',
                'sorties.nom',
                'sorties.dateHeureDebut',
                'sorties.dateLimiteInscription',
                'sorties.nbInscriptionMax',
                'etats.libelle AS etat',
                'organisateurParticipant.nom AS organisateur',
                'COUNT(DISTINCT inscriptionParticipant.id) AS nbInscrits',
                "CASE WHEN inscriptionParticipant.isInscrit = true THEN 'X' ELSE '' END AS inscrit"
            )

            ->leftJoin(
                'sorties.inscriptions',
                'inscriptionParticipant',
                'WITH',
                'inscriptionParticipant.participant = :participantId'
            )

//            ->leftJoin('sorties.inscriptions', 'inscriptions')

            ->leftJoin('sorties.etats', 'etats')
            ->leftJoin('sorties.organisateur', 'organisateurParticipant')

            ->setParameter('participantId', $participantId)

            ->groupBy(
                'sorties.id',
                'etats.libelle',
                'organisateurParticipant.nom',
                'inscriptionParticipant.id'
            )

            ->getQuery()
            ->getArrayResult();
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
