<?php

namespace App\Helper;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Organisasi\Kantor;
use App\Entity\Pegawai\JabatanPegawai;
use Doctrine\ORM\EntityManagerInterface;

class PosisiHelper
{
    private const TIPE_KANTOR_WITH_SAME_ATASAN_KANTOR_FOR_ECHELON_THREE = [
        'KANWIL',
        'KPDJP',
    ];

    private const TIPE_KANTOR_KP2KP = [
        'KP2KP'
    ];

    private const SETDITJEN = [
        'Sekretariat Direktorat Jenderal'
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
     * @param String|null $keyword
     * @return array
     */
    public function getAtasanFromJabatanPegawai(JabatanPegawai $jabatanPegawai, ?string $keyword): array
    {
        $jabatan        = $jabatanPegawai->getJabatan();
        $kantor         = $jabatanPegawai->getKantor();
        $unit           = $jabatanPegawai->getUnit();
        $pegawai        = $jabatanPegawai->getPegawai();
        $jenisJabatan   = $jabatan?->getJenis();
        $levelJabatan   = $jabatan?->getLevel();
        $tipeKantor     = $kantor?->getJenisKantor()?->getTipe();
        $jenisKantor    = $kantor?->getJenisKantor()?->getNama();
        $pangkat        = $pegawai?->getPangkat();

        $jabatanPegawaiAtasan = null;

        // Get the parent Unit & Kantor data
        $parentUnit     = $unit?->getParent();
        $parentKantor   = $kantor?->getParent();
        $kantorPembina  = $kantor?->getPembina();
        $unitPembina    = $unit?->getPembina();
        $parentNama     = $parentKantor?->getNama();

        if (isset($kantorPembina)) {
            $parentKantor = $kantorPembina;
        }
        if (isset($unitPembina)) {
            $parentUnit = $unitPembina;
        }

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

            // For echelon 3 and above, their boss must be at parent kantor
            if (3 >= $levelJabatan) {
                // For echelon 3 at Kanwil and Directorate, Atasan must be in the same Kantor
                if (3 === $levelJabatan
                    && in_array(
                        $tipeKantor,
                        self::TIPE_KANTOR_WITH_SAME_ATASAN_KANTOR_FOR_ECHELON_THREE,
                        true
                    ) && !in_array(
                        $parentNama,
                        self::SETDITJEN,
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
                    $jabatanPegawaiAtasan = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $parentKantor?->getId(),
                            $parentUnit?->getId(),
                            $tingkatEselonAtasan
                        );
                }
            } elseif (4 === $levelJabatan) {
                if (in_array(
                    $jenisKantor,
                    self::TIPE_KANTOR_KP2KP,
                    true
                )) {
                    $jabatanPegawaiAtasan = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $parentKantor?->getId(),
                            $parentUnit?->getId(),
                            $tingkatEselonAtasan
                        );
                }else{
                    $jabatanPegawaiAtasan = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $kantor?->getId(),
                            $parentUnit?->getId(),
                            $tingkatEselonAtasan
                        );
                }
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
            // Method to fetch atasan from jabatan pegawai (Cuti)
            if ('atasanCuti' === $keyword) {
                // Employee not on kanwil
                if (('KANWIL' !== $tipeKantor) && $kantor !== null) {
                    return $this->getKepalaKantorFromKantor($kantor);
                }

                // For employee with Golongan IV
                if (false !== stripos($pangkat, 'IV')) {
                    return $this->getKepalaKantorFromKantor($kantor);
                }

                $jabatanPegawaiAtasan = $this->entityManager
                    ->getRepository(JabatanPegawai::class)
                    ->findKabagUmumKanwilFromKantorEselon(
                        $kantor?->getId(),
                        3
                    );
            // Generic case
            } else {
                return $this->getKepalaKantorFromKantor($kantor);
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
     * method to fetch pejabat yang berwenang
     * @param JabatanPegawai $jabatanPegawai
     * @param String|null $keyword
     * @return array
     */
    public function getPybFromJabatanPegawai(JabatanPegawai $jabatanPegawai, ?string $keyword): array
    {
        $jabatan        = $jabatanPegawai->getJabatan();
        $kantor         = $jabatanPegawai->getKantor();
        $unit           = $jabatanPegawai->getUnit();
        $pegawai        = $jabatanPegawai->getPegawai();
        $jenisJabatan   = $jabatan?->getJenis();
        $levelJabatan   = $jabatan?->getLevel();
        $tipeKantor     = $kantor?->getJenisKantor()?->getTipe();
        $jenisKantor    = $kantor?->getJenisKantor()?->getNama();
        $pangkat        = $pegawai?->getPangkat();

        $jabatanPegawaiPyb = null;

        // Get the parent Unit & Kantor data
        $parentUnit     = $unit?->getParent();
        $parentKantor   = $kantor?->getParent();
        $kantorPembina  = $kantor?->getPembina();
        $unitPembina    = $unit?->getPembina();
        $parentNama     = $parentKantor?->getNama();

        if (isset($kantorPembina)) {
            $parentKantor = $kantorPembina;
        }
        if (isset($unitPembina)) {
            $parentUnit = $unitPembina;
        }

        if ('STRUKTURAL' === $jenisJabatan) {
            // Define the atasan level based on current level
            if (0 >= $levelJabatan) {
                $tingkatEselonPyb = 0;
            } elseif (6 === $levelJabatan) {
                if ('pybIzin' === $keyword) {
                    $tingkatEselonPyb = 4;
                } else {
                    $tingkatEselonPyb = 3;
                }
            } else {
                $tingkatEselonPyb = $levelJabatan - 1;
            }

            // For echelon 3 and above, their boss must be at parent kantor
            if (3 >= $levelJabatan) {
                // For echelon 3 at Kanwil and Directorate, Atasan must be in the same Kantor
                if (3 === $levelJabatan
                    && in_array(
                        $tipeKantor,
                        self::TIPE_KANTOR_WITH_SAME_ATASAN_KANTOR_FOR_ECHELON_THREE,
                        true
                    ) && !in_array(
                        $parentNama,
                        self::SETDITJEN,
                        true
                    )
                ) {
                    $jabatanPegawaiPyb = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $kantor?->getId(),
                            $parentUnit?->getId(),
                            $tingkatEselonPyb
                        );
                    // The others, atasan is at parent kantor
                } else {
                    $jabatanPegawaiPyb = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $parentKantor?->getId(),
                            $parentUnit?->getId(),
                            $tingkatEselonPyb
                        );
                }
            } elseif (4 === $levelJabatan) {
                //Pyb Izin, BA
                if ('pybIzin' === $keyword) {
                    if (!in_array(
                        $jenisKantor,
                        self::TIPE_KANTOR_KP2KP,
                        true
                    )) {
                        $jabatanPegawaiPyb = $this->entityManager
                            ->getRepository(JabatanPegawai::class)
                            ->findJabatanPegawaiActiveFromKantorUnitEselon(
                                $kantor?->getId(),
                                $parentUnit?->getId(),
                                $tingkatEselonPyb
                            );
                    } else {
                        return $this->getKepalaKantorFromKantor($parentKantor);
                    }
                // untuk pyb non Izin, BA
                } else {
                    //For Kanwil and Directorate, but not below setditjen
                    if ($kantor !== null
                        && !in_array(
                            $parentNama,
                            self::SETDITJEN,
                            true)
                        && !in_array(
                            $jenisKantor,
                            self::TIPE_KANTOR_KP2KP,
                            true)
                    ) {
                        return $this->getKepalaKantorFromKantor($kantor);
                    }

                    // The others, atasan is at parent kantor
                    return $this->getKepalaKantorFromKantor($parentKantor);
                }
            // Pelaksana izin
            } elseif ('pybIzin' === $keyword) {
                $jabatanPegawaiPyb = $this->entityManager
                    ->getRepository(JabatanPegawai::class)
                    ->findJabatanPegawaiActiveFromKantorUnitEselon(
                        $kantor?->getId(),
                        $unit?->getId(),
                        $tingkatEselonPyb
                    );
            // Pelaksana non izin
            } elseif (!in_array(
                $jenisKantor,
                self::TIPE_KANTOR_KP2KP,
                true
            )) {
                if ('pybCutiDiatur' === $keyword &&
                    in_array(
                        $parentNama,
                        self::SETDITJEN,
                        true
                    )
                ) {
                    $jabatanPegawaiPyb = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiByKantorAndTingkat('f5c2c27b-5adc-4c1f-bc6d-aaee3cc99d56', 3);
                } else {
                    $jabatanPegawaiPyb = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $kantor?->getId(),
                            $parentUnit?->getId(),
                            $tingkatEselonPyb
                        );
                }
                return [$parentKantor?->getId(),
                    $parentUnit?->getId(),
                    $tipeKantor,
                    $tingkatEselonPyb];
            } else {
                $jabatanPegawaiPyb = $this->entityManager
                    ->getRepository(JabatanPegawai::class)
                    ->findJabatanPegawaiActiveFromKantorUnitEselon(
                        $parentKantor?->getId(),
                        $parentUnit?->getId(),
                        $tingkatEselonPyb
                    );
            }
        // For the fungsional
        } elseif ('FUNGSIONAL' === $jenisJabatan) {
            // For fungsional on Setditjen
            if (self::SETDITJEN === $parentNama
                || self::TIPE_KANTOR_KP2KP === $jenisKantor
            ) {
                return $this->getKepalaKantorFromKantor($parentKantor);
            }

            // Method to fetch pyb from jabatan pegawai (Cuti CS+14, CB, CBS, CAP)
            if ('pybCutiDiatur' === $keyword) {
                // For fungsional not in kanwil
                if ('KANWIL' !== $tipeKantor) {
                    return $this->getKepalaKantorFromKantor($kantor);
                }

                // For employee with Golongan IV
                if (false !== stripos($pangkat, 'IV')) {
                    return $this->getKepalaKantorFromKantor($kantor);
                }

                $jabatanPegawaiPyb = $this->entityManager
                    ->getRepository(JabatanPegawai::class)
                    ->findKabagUmumKanwilFromKantorEselon(
                        $kantor?->getId(),
                        3
                    );
            } else {
                return $this->getKepalaKantorFromKantor($kantor);
            }
        }

        // Make sure the output is instanceof JabatanPegawai
        if ($jabatanPegawaiPyb instanceof JabatanPegawai) {
            return $this->makeOutputSinglePegawaiFromJabatanPegawai($jabatanPegawaiPyb);
        }

        return ['No Pyb found.'];
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
                $kantor->getId(),
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
            'userIri' => $this->iriConverter->getIriFromResource($jabatanPegawai->getPegawai()?->getUser()),
            'usedId' => $jabatanPegawai->getPegawai()?->getUser()?->getId(),
            'userIdentifier' => $jabatanPegawai->getPegawai()?->getUser()?->getUserIdentifier(),
            'pegawaiIri' => $this->iriConverter->getIriFromResource($jabatanPegawai->getPegawai()),
            'pegawaiId' => $jabatanPegawai->getPegawai()?->getId(),
            'name' => $jabatanPegawai->getPegawai()?->getNama(),
            'nip9' => $jabatanPegawai->getPegawai()?->getNip9(),
            'nip18' => $jabatanPegawai->getPegawai()?->getNip18(),
            'jabatanIri' => $this->iriConverter->getIriFromResource($jabatanPegawai->getJabatan()),
            'jabatanId' => $jabatanPegawai->getJabatan()?->getId(),
            'jabatanName' => $jabatanPegawai->getJabatan()?->getNama(),
            'kantorIri' => $this->iriConverter->getIriFromResource($jabatanPegawai->getKantor()),
            'kantorId' => $jabatanPegawai->getKantor()?->getId(),
            'kantorName' => $jabatanPegawai->getKantor()?->getNama(),
            'unitIri' => $this->iriConverter->getIriFromResource($jabatanPegawai->getUnit()),
            'unitId' => $jabatanPegawai->getUnit()?->getId(),
            'unitName' => $jabatanPegawai->getUnit()?->getNama(),
            'eselonName' => $jabatanPegawai->getJabatan()?->getEselon()?->getNama(),
            'eselonKode' => $jabatanPegawai->getJabatan()?->getEselon()?->getKode(),
        ];
    }
}
