<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\Kantor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Kantor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Kantor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Kantor[]    findAll()
 * @method Kantor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KantorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Kantor::class);
    }

    // /**
    //  * @return Kantor[] Returns an array of Kantor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Kantor
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
