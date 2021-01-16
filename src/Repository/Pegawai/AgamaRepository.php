<?php

namespace App\Repository\Pegawai;

use App\Entity\Pegawai\Agama;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Agama|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agama|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agama[]    findAll()
 * @method Agama[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgamaRepository extends ServiceEntityRepository
{
    /**
     * AgamaRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agama::class);
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findMaxLegacyCode(): mixed
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'select max(legacy_kode) maxid from agama';

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchOne();
    }

    // /**
    //  * @return Agama[] Returns an array of Agama objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Agama
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
