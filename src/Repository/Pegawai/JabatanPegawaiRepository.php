<?php

namespace App\Repository\Pegawai;

use App\Entity\Pegawai\JabatanPegawai;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JabatanPegawai|null find($id, $lockMode = null, $lockVersion = null)
 * @method JabatanPegawai|null findOneBy(array $criteria, array $orderBy = null)
 * @method JabatanPegawai[]    findAll()
 * @method JabatanPegawai[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JabatanPegawaiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JabatanPegawai::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findJabatanPegawaiActiveFromKantorUnitEselon($kantorId, $unitId, $eselonTingkat)
    {
        return $this->createQueryBuilder('jp')
            ->leftJoin('jp.kantor', 'kantor')
            ->leftJoin('jp.unit', 'unit')
            ->leftJoin('jp.jabatan', 'jabatan')
            ->leftJoin('jabatan.eselon', 'eselon')
            ->andWhere('kantor.id = :kantorId')
            ->andWhere('unit.id = :unitId')
            ->andWhere('eselon.tingkat = :eselonTingkat')
            ->andWhere('jp.tanggalMulai < :now')
            ->andWhere('jp.tanggalSelesai is null or jp.tanggalSelesai > :now')
            ->setParameter('kantorId', $kantorId)
            ->setParameter('unitId', $unitId)
            ->setParameter('eselonTingkat', $eselonTingkat)
            ->setParameter('now', new DateTime('now'))
            ->setMaxResults(1)
            ->addOrderBy('jp.tanggalMulai', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findKabagUmumKanwilFromKantorEselon($kantorId, $eselonTingkat)
    {
        return $this->createQueryBuilder('jp')
            ->leftJoin('jp.kantor', 'kantor')
            ->leftJoin('jp.unit', 'unit')
            ->leftJoin('jp.jabatan', 'jabatan')
            ->leftJoin('jabatan.eselon', 'eselon')
            ->andWhere('kantor.id = :kantorId')
            ->andWhere('unit.nama = :unitNama')
            ->andWhere('eselon.tingkat = :eselonTingkat')
            ->andWhere('jp.tanggalMulai < :now')
            ->andWhere('jp.tanggalSelesai is null or jp.tanggalSelesai > :now')
            ->setParameter('kantorId', $kantorId)
            ->setParameter('unitNama', 'Bagian Umum')
            ->setParameter('eselonTingkat', $eselonTingkat)
            ->setParameter('now', new DateTime('now'))
            ->setMaxResults(1)
            ->addOrderBy('jp.tanggalMulai', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findJabatanPegawaiActiveFromKantorAndEselon($kantorId, $eselonTingkat)
    {
        return $this->createQueryBuilder('jp')
            ->leftJoin('jp.kantor', 'kantor')
            ->leftJoin('jp.jabatan', 'jabatan')
            ->leftJoin('jabatan.eselon', 'eselon')
            ->andWhere('kantor.id = :kantorId')
            ->andWhere('eselon.tingkat = :eselonTingkat')
            ->andWhere('jp.tanggalMulai < :now')
            ->andWhere('jp.tanggalSelesai is null or jp.tanggalSelesai > :now')
            ->setParameter('kantorId', $kantorId)
            ->setParameter('eselonTingkat', $eselonTingkat)
            ->setParameter('now', new DateTime('now'))
            ->setMaxResults(1)
            ->addOrderBy('jp.tanggalMulai', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findJabatanPegawaiByKantorAndTingkat(string $kantorId, int $tingkat)
    {
        return $this->createQueryBuilder('jp')
            ->leftJoin('jp.kantor', 'kantor')
            ->leftJoin('jp.jabatan', 'jabatan')
            ->leftJoin('jabatan.eselon', 'eselon')
            ->andWhere('kantor.id = :kantorId')
            ->andWhere('eselon.tingkat = :eselonTingkat')
            ->andWhere('jp.tanggalMulai < :now')
            ->andWhere('jp.tanggalSelesai is null or jp.tanggalSelesai > :now')
            ->setParameter('kantorId', $kantorId)
            ->setParameter('eselonTingkat', $tingkat)
            ->setParameter('now', new DateTime('now'))
            ->setMaxResults(1)
            ->addOrderBy('jp.tanggalMulai', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
