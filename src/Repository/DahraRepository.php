<?php

namespace App\Repository;

use App\Entity\Dahra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dahra>
 *
 * @method Dahra|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dahra|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dahra[]    findAll()
 * @method Dahra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DahraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dahra::class);
    }

//    /**
//     * @return Dahra[] Returns an array of Dahra objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Dahra
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
