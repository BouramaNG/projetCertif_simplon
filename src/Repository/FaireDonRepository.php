<?php

namespace App\Repository;

use App\Entity\FaireDon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FaireDon>
 *
 * @method FaireDon|null find($id, $lockMode = null, $lockVersion = null)
 * @method FaireDon|null findOneBy(array $criteria, array $orderBy = null)
 * @method FaireDon[]    findAll()
 * @method FaireDon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaireDonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FaireDon::class);
    }

//    /**
//     * @return FaireDon[] Returns an array of FaireDon objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FaireDon
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
