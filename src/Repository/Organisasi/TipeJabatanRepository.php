<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\TipeJabatan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TipeJabatan|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipeJabatan|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipeJabatan[]    findAll()
 * @method TipeJabatan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipeJabatanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipeJabatan::class);
    }

    // /**
    //  * @return TipeJabatan[] Returns an array of TipeJabatan objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TipeJabatan
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
