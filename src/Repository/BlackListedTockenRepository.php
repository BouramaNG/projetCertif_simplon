<?php

namespace App\Repository;

use App\Entity\BlackListedTocken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlackListedTocken>
 *
 * @method BlackListedTocken|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlackListedTocken|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlackListedTocken[]    findAll()
 * @method BlackListedTocken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlackListedTockenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlackListedTocken::class);
    }

//    /**
//     * @return BlackListedTocken[] Returns an array of BlackListedTocken objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BlackListedTocken
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
