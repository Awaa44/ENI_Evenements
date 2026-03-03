<?php

namespace App\Repository;

use App\Entity\Lieux;
use App\Entity\Sorties;
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

    public function findNameVille($nomVille): array
    {
        return $this->createQueryBuilder('villes')
            ->select(
                'villes.id',
                'villes.nomVille',
                'villes.codePostal'
            )
            ->andWhere('villes.nomVille LIKE :nomVille')
            ->setParameter('nomVille', '%' . $nomVille . '%')
            ->getQuery()
            ->getArrayResult();
    }

    public function canBeDeleted(Villes $ville): bool
    {
        $em = $this->getEntityManager();

        // 1️⃣ Récupérer les lieux liés à la ville
        $lieux = $em->getRepository(Lieux::class)
            ->createQueryBuilder('l')
            ->select('l.id')
            ->where('l.villes = :ville')
            ->setParameter('ville', $ville)
            ->getQuery()
            ->getScalarResult();

        if (empty($lieux)) {
            return true; // Aucun lieu → suppression OK
        }

        $lieuxIds = array_column($lieux, 'id');

        // 2️⃣ Vérifier si ces lieux sont utilisés dans Sorties
        $count = $em->getRepository(Sorties::class)
            ->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.lieux IN (:lieux)')
            ->setParameter('lieux', $lieuxIds)
            ->getQuery()
            ->getSingleScalarResult();

        return $count == 0;
    }

    public function deleteVilleWithLieux(Villes $ville): void
    {
        $em = $this->getEntityManager();

        $lieux = $em->getRepository(Lieux::class)
            ->findBy(['villes' => $ville]);

        foreach ($lieux as $lieu) {
            $em->remove($lieu);
        }

        $em->remove($ville);
        $em->flush();
    }
}
