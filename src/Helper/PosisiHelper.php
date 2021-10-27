<?php

namespace App\Helper;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Pegawai\JabatanPegawai;
use Doctrine\ORM\EntityManagerInterface;

class PosisiHelper
{
    private const TIPE_KANTOR_WITH_SAME_ATASAN_KANTOR_FOR_ECHELON_THREE = [
        'KANWIL',
        'KPDJP',
    ];

    private const TIPE_KANTOR_JABATAN_FUNGSIONAL_UNDER_ECHELON_THREE = [
        'KPP',
        'UPT',
    ];

    // Check the group name from App\Entity\Organisasi\GroupJabatan
    private const GROUP_JABATAN_FUNGSIONAL_UNDER_KEPALA_KANTOR = [
        'Fungsional Pemeriksa Pajak',
        'Fungsional Pranata Komputer',
    ];

    private IriConverterInterface $iriConverter;
    private EntityManagerInterface $entityManager;

    public function __construct(IriConverterInterface $iriConverter, EntityManagerInterface $entityManager)
    {
        $this->iriConverter = $iriConverter;
        $this->entityManager = $entityManager;
    }

    /**
     * @param JabatanPegawai $jabatanPegawai
     * @return array
     */
    public function getAtasanFromJabatanPegawai(JabatanPegawai $jabatanPegawai): array
    {
        $jabatan = $jabatanPegawai->getJabatan();
        $kantor = $jabatanPegawai->getKantor();
        $unit = $jabatanPegawai->getUnit();
        $jenisJabatan = $jabatan?->getJenis();
        $levelJabatan = $jabatan?->getLevel();
        $tipeKantor = $kantor?->getJenisKantor()?->getTipe();

        $jabatanPegawaiAtasan = null;

        // For Struktural type
        if ('STRUKTURAL' === $jenisJabatan) {
            // Define the atasan level based on current level
            if (0 >= $levelJabatan) {
                $tingkatEselonAtasan = 0;
            } elseif (6 === $levelJabatan) {
                $tingkatEselonAtasan = 4;
            } else {
                $tingkatEselonAtasan = $levelJabatan - 1;
            }

            // Get the parent Unit data
            $parentUnit = $unit?->getParent();

            // For echelon 3 and above, their boss must be at parent kantor
            if (3 >= $levelJabatan) {
                // For echelon 3 at Kanwil and Directorate, Atasan must be in the same Kantor
                if (3 === $levelJabatan
                    && in_array(
                        $tipeKantor,
                        self::TIPE_KANTOR_WITH_SAME_ATASAN_KANTOR_FOR_ECHELON_THREE,
                        true
                    )
                ) {
                    $jabatanPegawaiAtasan = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $kantor?->getId(),
                            $parentUnit?->getId(),
                            $tingkatEselonAtasan
                        );

                // The others, atasan is at parent kantor
                } else {
                    $parentKantor = $kantor?->getParent();
                    $jabatanPegawaiAtasan = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $parentKantor?->getId(),
                            $parentUnit?->getId(),
                            $tingkatEselonAtasan
                        );
                }
            } elseif (4 === $levelJabatan) {
                $jabatanPegawaiAtasan = $this->entityManager
                    ->getRepository(JabatanPegawai::class)
                    ->findJabatanPegawaiActiveFromKantorUnitEselon(
                        $kantor?->getId(),
                        $parentUnit?->getId(),
                        $tingkatEselonAtasan
                    );
            } else {
                $jabatanPegawaiAtasan = $this->entityManager
                    ->getRepository(JabatanPegawai::class)
                    ->findJabatanPegawaiActiveFromKantorUnitEselon(
                        $kantor?->getId(),
                        $unit?->getId(),
                        $tingkatEselonAtasan
                    );
            }

        // For the fungsional
        } elseif ('FUNGSIONAL' === $jenisJabatan) {
            $groupJabatanName = $jabatan?->getGroupJabatan()?->getNama();

            // For fungsional under kepala kantor
            if (in_array(
                $groupJabatanName,
                self::GROUP_JABATAN_FUNGSIONAL_UNDER_KEPALA_KANTOR,
                true
            )) {
                // Differentiate between fungsional under echelon 3 and echelon 2
                if (in_array(
                    $tipeKantor,
                    self::TIPE_KANTOR_JABATAN_FUNGSIONAL_UNDER_ECHELON_THREE,
                    true
                )) {
                    $tingkatEselonAtasan = 3;
                } else {
                    $tingkatEselonAtasan = 2;
                }

                $jabatanPegawaiAtasan = $this->entityManager
                    ->getRepository(JabatanPegawai::class)
                    ->findJabatanPegawaiActiveFromKantorAndEselon(
                        $kantor?->getId(),
                        $tingkatEselonAtasan
                    );
            } else {
                $jabatanPegawaiAtasan = $this->entityManager
                    ->getRepository(JabatanPegawai::class)
                    ->findJabatanPegawaiActiveFromKantorUnitEselon(
                        $kantor?->getId(),
                        $unit?->getId(),
                        4
                    );
            }
        }

        // Make sure the output is instanceof JabatanPegawai
        if ($jabatanPegawaiAtasan instanceof JabatanPegawai) {
            return [
                'userIri' => $this->iriConverter->getIriFromItem($jabatanPegawaiAtasan->getPegawai()?->getUser()),
                'userIdentifier' => $jabatanPegawaiAtasan->getPegawai()?->getUser()?->getUserIdentifier(),
                'pegawaiIri' => $this->iriConverter->getIriFromItem($jabatanPegawaiAtasan->getPegawai()),
                'name' => $jabatanPegawaiAtasan->getPegawai()?->getNama(),
                'nip9' => $jabatanPegawaiAtasan->getPegawai()?->getNip9(),
                'nip18' => $jabatanPegawaiAtasan->getPegawai()?->getNip18(),
                'jabatanIri' => $this->iriConverter->getIriFromItem($jabatanPegawaiAtasan->getJabatan()),
                'jabatanName' => $jabatanPegawaiAtasan->getJabatan()?->getNama(),
                'kantorIri' => $this->iriConverter->getIriFromItem($jabatanPegawaiAtasan->getKantor()),
                'kantorName' => $jabatanPegawaiAtasan->getKantor()?->getNama(),
                'unitIri' => $this->iriConverter->getIriFromItem($jabatanPegawaiAtasan->getUnit()),
                'unitName' => $jabatanPegawaiAtasan->getUnit()?->getNama(),
                'eselonTingkat' => $jabatanPegawaiAtasan->getJabatan()?->getEselon()?->getTingkat()
            ];
        }

        // If no result found
        return ['no atasan found.'];
    }
}
