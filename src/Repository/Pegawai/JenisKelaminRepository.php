<?php

namespace App\Repository\Pegawai;

use App\Entity\Pegawai\JenisKelamin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JenisKelamin|null find($id, $lockMode = null, $lockVersion = null)
 * @method JenisKelamin|null findOneBy(array $criteria, array $orderBy = null)
 * @method JenisKelamin[]    findAll()
 * @method JenisKelamin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JenisKelaminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JenisKelamin::class);
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findMaxLegacyCode(): mixed
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'select max(legacy_kode) maxid from jenis_kelamin';

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchOne();
    }

    // /**
    //  * @return JenisKelamin[] Returns an array of JenisKelamin objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JenisKelamin
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
