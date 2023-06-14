<?php

namespace App\Repository\Pegawai;

use App\Entity\Pegawai\JabatanPegawai;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
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

    /**
     * @throws Exception
     */
    public function findRoleCombinationByPegawai($pegawaiId): mixed
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT string_agg(nama, ',') as role
            FROM (SELECT nama
                  FROM (SELECT r2.nama,
                               r2.id role_id,
                               a.*,
                               jabatan_adhoc
                        FROM (SELECT pegawai_id,
                                     jabatan_id
                              FROM jabatan_pegawai p
                                       LEFT JOIN jabatan j
                                                 ON p.jabatan_id = j.id
                              WHERE jenis IN ('STRUKTURAL', 'FUNGSIONAL')) a
                                 LEFT JOIN (SELECT pegawai_id,
                                                   jabatan_id jabatan_adhoc
                                            FROM jabatan_pegawai p
                                                     LEFT JOIN jabatan j
                                                               ON p.jabatan_id = j.id
                                            WHERE jenis IN ('ADHOC')) b
                                           ON b.pegawai_id = a.pegawai_id
                                 JOIN role_jabatan rj
                                      ON rj.jabatan_id = a.jabatan_id
                                 join role r2
                                      ON r2.id = rj.role_id
                                          AND r2.jenis IN (2, 8, 9, 10, 11, 12)
                                          AND (r2.end_date IS NULL OR now() <= r2.end_date)
                                          AND r2.operator = true) x
                  WHERE role_id
                      IN (SELECT role_id
                          FROM role_jabatan y
                          WHERE y.jabatan_id = jabatan_adhoc)
                    AND pegawai_id = :pegawai_id) y
        ";

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'pegawai_id' => $pegawaiId,
        ]);

        return $resultSet->fetchAssociative();
    }
}
