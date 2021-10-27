<?php

namespace App\Helper;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Organisasi\Kantor;
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
     * Method to fetch atasan from jabatan pegawai
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
            return $this->makeOutputSinglePegawaiFromJabatanPegawai($jabatanPegawaiAtasan);
        }

        // If no result found
        return ['No atasan found.'];
    }

    /**
     * Method to fetch Kepala Kantor from Kantor Data
     * @param Kantor $kantor
     * @return array
     */
    public function getKepalaKantorFromKantor(Kantor $kantor): array
    {
        $jabatanPegawaiKepalaKantor = $this->entityManager
            ->getRepository(JabatanPegawai::class)
            ->findJabatanPegawaiActiveFromKantorAndEselon(
                $kantor?->getId(),
                $kantor->getLevel()
            );

        if (null === $jabatanPegawaiKepalaKantor) {
            return ['No Kepala Kantor found.'];
        }

        if ($jabatanPegawaiKepalaKantor instanceof JabatanPegawai) {
            return $this->makeOutputSinglePegawaiFromJabatanPegawai($jabatanPegawaiKepalaKantor);
        }

        return ['No Kepala Kantor found.'];
    }

    /**
     * Method to create single output of pegawai data from jabatan pegawai
     * @param JabatanPegawai $jabatanPegawai
     * @return array
     */
    private function makeOutputSinglePegawaiFromJabatanPegawai(JabatanPegawai $jabatanPegawai): array
    {
        return [
            'userIri' => $this->iriConverter->getIriFromItem($jabatanPegawai->getPegawai()?->getUser()),
            'usedId' => $jabatanPegawai->getPegawai()?->getUser()?->getId(),
            'userIdentifier' => $jabatanPegawai->getPegawai()?->getUser()?->getUserIdentifier(),
            'pegawaiIri' => $this->iriConverter->getIriFromItem($jabatanPegawai->getPegawai()),
            'pegawaiId' => $jabatanPegawai->getPegawai()?->getId(),
            'name' => $jabatanPegawai->getPegawai()?->getNama(),
            'nip9' => $jabatanPegawai->getPegawai()?->getNip9(),
            'nip18' => $jabatanPegawai->getPegawai()?->getNip18(),
            'jabatanIri' => $this->iriConverter->getIriFromItem($jabatanPegawai->getJabatan()),
            'jabatanId' => $jabatanPegawai->getJabatan()?->getId(),
            'jabatanName' => $jabatanPegawai->getJabatan()?->getNama(),
            'kantorIri' => $this->iriConverter->getIriFromItem($jabatanPegawai->getKantor()),
            'kantorId' => $jabatanPegawai->getKantor()?->getId(),
            'kantorName' => $jabatanPegawai->getKantor()?->getNama(),
            'unitIri' => $this->iriConverter->getIriFromItem($jabatanPegawai->getUnit()),
            'unitId' => $jabatanPegawai->getUnit()?->getId(),
            'unitName' => $jabatanPegawai->getUnit()?->getNama(),
            'eselonName' => $jabatanPegawai->getJabatan()?->getEselon()?->getNama(),
            'eselonKode' => $jabatanPegawai->getJabatan()?->getEselon()?->getKode(),
        ];
    }
}