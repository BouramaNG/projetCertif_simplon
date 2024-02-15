<?php

namespace App\Repository;

use App\Entity\Parrainage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parrainage>
 *
 * @method Parrainage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parrainage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parrainage[]    findAll()
 * @method Parrainage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParrainageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parrainage::class);
    }

//    /**
//     * @return Parrainage[] Returns an array of Parrainage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Parrainage
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
