<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\JenisKantor;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JenisKantor|null find($id, $lockMode = null, $lockVersion = null)
 * @method JenisKantor|null findOneBy(array $criteria, array $orderBy = null)
 * @method JenisKantor[]    findAll()
 * @method JenisKantor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JenisKantorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JenisKantor::class);
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public function findJenisKantorByNameKeyword($keyword): mixed
    {
        $lowerKeyword = strtolower($keyword);
        return $this->createQueryBuilder('j')
            ->andWhere('lower(j.nama) LIKE :val')
            ->setParameter('val', '%' . $lowerKeyword . '%')
            ->addOrderBy('j.klasifikasi', 'ASC')
            ->addOrderBy('j.nama', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function findAllActiveJenisKantor(): mixed
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.tanggalAktif < :now')
            ->andWhere('j.tanggalNonaktif is null or j.tanggalNonaktif > :now')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('j.klasifikasi', 'ASC')
            ->addOrderBy('j.nama', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public function findActiveJenisKantorByNameKeyword($keyword): mixed
    {
        $lowerKeyword = strtolower($keyword);
        return $this->createQueryBuilder('j')
            ->andWhere('lower(j.nama) LIKE :val')
            ->andWhere('j.tanggalAktif < :now')
            ->andWhere('j.tanggalNonaktif is null or j.tanggalNonaktif > :now')
            ->setParameter('val', '%' . $lowerKeyword . '%')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('j.klasifikasi', 'ASC')
            ->addOrderBy('j.nama', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
