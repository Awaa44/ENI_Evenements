<?php

namespace App\Repository;

use App\Entity\Inscriptions;
use App\Entity\Participants;
use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inscriptions>
 */
class InscriptionsRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscriptions::class);
    }

    public function updateInscription($idSortie): int
    {
        return $this->createQueryBuilder('inscriptions')
            ->update()
            ->set('inscriptions.isInscrit', ':value')
            ->where('inscriptions.sortie = :idSortie')
            ->setParameter('value', false)
            ->setParameter('idSortie', $idSortie)
            ->getQuery()
            ->execute();
    }
//    /**
//     * @return Inscriptions[] Returns an array of Inscriptions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Inscriptions
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
