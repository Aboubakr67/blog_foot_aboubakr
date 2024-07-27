<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    // Pagination (ORDER BY createdAt sinon updatedAt)
    public function findAllSorted()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('COALESCE(a.updatedAt, a.createdAt) AS HIDDEN sortDate')
            ->orderBy('sortDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // public function countAvisPerGame(): array
    // {
    //     $qb = $this->createQueryBuilder('a')
    //         ->select('g.equipeDomicile AS domicile, g.equipeExterieur AS exterieur, COUNT(a.id) AS nbAvis')
    //         ->join('a.game', 'g')
    //         ->groupBy('g.title')
    //         ->getQuery();

    //     return $qb->getResult();
    // }

    public function countAvisPerGame(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('td.name AS domicile, 
                      te.name AS exterieur,
                      COUNT(a.id) AS nbAvis')
            ->join('a.game', 'g')
            ->leftJoin('g.equipeDomicile', 'td')
            ->leftJoin('g.equipeExterieur', 'te')
            ->groupBy('g.id, td.name, te.name')
            ->getQuery();

        return $qb->getResult();
    }

    //    /**
    //     * @return Avis[] Returns an array of Avis objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Avis
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
