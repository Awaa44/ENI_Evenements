<?php

namespace App\Repository;

use App\Entity\Villes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Villes>
 */
class VillesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Villes::class);
    }

//        public function findVille($nomVille): array
//        {
//            return $this->createQueryBuilder('v')
//                ->andWhere('sorties.nom LIKE :nomSortie')
//                ->setParameter('nomSortie', '%' . $nomVille . '%');
//                ->getQuery()
//                ->getOneOrNullResult()
//            ;
//        }
}
