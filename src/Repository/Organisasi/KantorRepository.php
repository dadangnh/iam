<?php

namespace App\Repository\Organisasi;

use App\Entity\Organisasi\Kantor;
use DateTime;
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

    /**
     * @param $keyword
     * @return mixed
     */
    public function findKantorByNameKeyword($keyword): mixed
    {
        $lowerKeyword = strtolower($keyword);
        return $this->createQueryBuilder('k')
            ->andWhere('lower(k.nama) LIKE :val')
            ->setParameter('val', '%' . $lowerKeyword . '%')
            ->addOrderBy('k.level', 'ASC')
            ->addOrderBy('k.nama', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function findAllActiveKantor(): mixed
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.tanggalAktif < :now')
            ->andWhere('k.tanggalNonaktif is null or k.tanggalNonaktif > :now')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('k.level', 'ASC')
            ->addOrderBy('k.nama', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public function findActiveKantorByNameKeyword($keyword): mixed
    {
        $lowerKeyword = strtolower($keyword);
        return $this->createQueryBuilder('k')
            ->andWhere('lower(k.nama) LIKE :val')
            ->andWhere('k.tanggalAktif < :now')
            ->andWhere('k.tanggalNonaktif is null or k.tanggalNonaktif > :now')
            ->setParameter('val', '%' . $lowerKeyword . '%')
            ->setParameter('now', new DateTime('now'))
            ->addOrderBy('k.level', 'ASC')
            ->addOrderBy('k.nama', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function findLegacyDataFromArrayOfIds($ids): mixed
    {
        return $this->createQueryBuilder('k')
            ->select([
                'k.id',
                'k.nama',
                'k.level',
                'k.legacyKode',
                'k.legacyKodeKpp',
                'k.legacyKodeKanwil'
            ])
            ->andWhere('k.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->addOrderBy('k.level', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
