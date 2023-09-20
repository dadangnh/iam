<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\KantorLuar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<KantorLuar>
 *
 * @method KantorLuar|null find($id, $lockMode = null, $lockVersion = null)
 * @method KantorLuar|null findOneBy(array $criteria, array $orderBy = null)
 * @method KantorLuar[]    findAll()
 * @method KantorLuar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KantorLuarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KantorLuar::class);
    }

//    /**
//     * @return KantorLuar[] Returns an array of KantorLuar objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('k')
//            ->andWhere('k.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('k.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?KantorLuar
//    {
//        return $this->createQueryBuilder('k')
//            ->andWhere('k.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
