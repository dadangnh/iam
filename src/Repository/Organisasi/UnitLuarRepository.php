<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\UnitLuar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UnitLuar>
 *
 * @method UnitLuar|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnitLuar|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnitLuar[]    findAll()
 * @method UnitLuar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnitLuarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnitLuar::class);
    }

//    /**
//     * @return UnitLuar[] Returns an array of UnitLuar objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UnitLuar
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
