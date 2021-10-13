<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\Jabatan;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Jabatan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jabatan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jabatan[]    findAll()
 * @method Jabatan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JabatanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jabatan::class);
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public function findJabatanByNameKeyword($keyword): mixed
    {
        $lowerKeyword = strtolower($keyword);
        return $this->createQueryBuilder('j')
            ->andWhere('lower(j.nama) LIKE :val')
            ->setParameter('val', '%' . $lowerKeyword . '%')
            ->addOrderBy('j.level', 'ASC')
            ->addOrderBy('j.nama', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function findAllActiveJabatan(): mixed
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.tanggalAktif < :now')
            ->andWhere('j.tanggalNonaktif is null or j.tanggalNonaktif > :now')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('j.level', 'ASC')
            ->addOrderBy('j.nama', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public function findActiveJabatanByNameKeyword($keyword): mixed
    {
        $lowerKeyword = strtolower($keyword);
        return $this->createQueryBuilder('j')
            ->andWhere('lower(j.nama) LIKE :val')
            ->andWhere('j.tanggalAktif < :now')
            ->andWhere('j.tanggalNonaktif is null or j.tanggalNonaktif > :now')
            ->setParameter('val', '%' . $lowerKeyword . '%')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('j.level', 'ASC')
            ->addOrderBy('j.nama', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
