<?php

namespace App\Repository;

use App\Entity\Talibe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Talibe>
 *
 * @method Talibe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Talibe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Talibe[]    findAll()
 * @method Talibe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TalibeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Talibe::class);
    }

//    /**
//     * @return Talibe[] Returns an array of Talibe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Talibe
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
