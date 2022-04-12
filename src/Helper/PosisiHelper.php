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

    private const TIPE_KANTOR_KP2KP = [
        'KP2KP'
    ];

    private const SETDITJEN = [
        'Sekretariat Direktorat Jenderal Pajak'
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
        $jabatan      = $jabatanPegawai->getJabatan();
        $kantor       = $jabatanPegawai->getKantor();
        $unit         = $jabatanPegawai->getUnit();
        $levelUnit    = $unit?->getLevel();
        $pegawai      = $jabatanPegawai->getPegawai();
        $jenisJabatan = $jabatan?->getJenis();
        $levelJabatan = $jabatan?->getLevel();
        $tipeKantor   = $kantor?->getJenisKantor()?->getTipe();
        $pangkat      = $pegawai?->getPangkat();

        $jabatanPegawaiAtasan = null;

        // Get the parent Unit & Kantor data
        $parentUnit     = $unit?->getParent();
        $parentKantor   = $kantor?->getParent();
        $kantorPembina  = $kantor?->getPembina();
        $unitPembina    = $unit?->getPembina();
        $parentNama     = $parentKantor?->getNama();

        if(isset($kantorPembina)){$parentKantor = $kantorPembina;}
        if(isset($unitPembina)){$parentUnit = $unitPembina;}

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
                    ) &&  !in_array(
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
            if (4 !== $levelUnit)
            {
                $jabatanPegawaiAtasan = self::getKepalaKantorFromKantor($kantor);
            } else {
                if (!in_array(
                    $tipeKantor,
                    self::TIPE_KANTOR_KP2KP,
                    true
                )
                ) {
                    $jabatanPegawaiAtasan = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $kantor?->getId(),
                            $parentUnit?->getId(),
                            3
                        );
                }else{
                    $jabatanPegawaiAtasan = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $parentKantor?->getId(),
                            $parentUnit?->getId(),
                            3
                        );
                }
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
     * Method to fetch atasan from jabatan pegawai (Cuti)
     * @param JabatanPegawai $jabatanPegawai
     * @return array
     */
    public function getAtasanCutiFromJabatanPegawai(JabatanPegawai $jabatanPegawai): array
    {
        $jabatan      = $jabatanPegawai->getJabatan();
        $kantor       = $jabatanPegawai->getKantor();
        $unit         = $jabatanPegawai->getUnit();
        $levelUnit      = $unit?->getLevel();
        $pegawai      = $jabatanPegawai->getPegawai();
        $jenisJabatan = $jabatan?->getJenis();
        $levelJabatan = $jabatan?->getLevel();
        $tipeKantor   = $kantor?->getJenisKantor()?->getTipe();
        $pangkat      = $pegawai?->getPangkat();

        $jabatanPegawaiAtasan = null;

        // Get the parent Unit & Kantor data
        $parentUnit     = $unit?->getParent();
        $parentKantor   = $kantor?->getParent();
        $kantorPembina  = $kantor?->getPembina();
        $unitPembina    = $unit?->getPembina();
        $parentNama     = $parentKantor?->getNama();

        if(isset($kantorPembina)){$parentKantor = $kantorPembina;}
        if(isset($unitPembina)){$parentUnit = $unitPembina;}

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
                    ) &&  !in_array(
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
            if (4 !== $levelUnit && 'KANWIL' !== $tipeKantor)
            {
                $jabatanPegawaiAtasan = self::getKepalaKantorFromKantor($kantor);
            } else {
                if (!in_array(
                    $tipeKantor,
                    self::TIPE_KANTOR_WITH_SAME_ATASAN_KANTOR_FOR_ECHELON_THREE,
                    true
                )
                ) {
                    if(preg_match("/IV/i", $pangkat)){
                        $jabatanPegawaiAtasan = self::getKepalaKantorFromKantor($kantor);
                    }else{
                        if(4 === $levelUnit){
                            $jabatanPegawaiAtasan = $this->entityManager
                                ->getRepository(JabatanPegawai::class)
                                ->findJabatanPegawaiActiveFromKantorUnitEselon(
                                    $kantor?->getId(),
                                    $unit?->getId(),
                                    4
                                );
                        }else{
                            $jabatanPegawaiAtasan = $this->entityManager
                                ->getRepository(JabatanPegawai::class)
                                ->findKabagUmumKanwilFromKantorEselon(
                                    $kantor?->getId(),
                                    3
                                );
                        }
                    }
                }else{
                    if(preg_match("/IV/i", $pangkat)){
                        $jabatanPegawaiAtasan = self::getKepalaKantorFromKantor($kantor);
                    }else {
                        $jabatanPegawaiAtasan = $this->entityManager
                            ->getRepository(JabatanPegawai::class)
                            ->findJabatanPegawaiActiveFromKantorUnitEselon(
                                $kantor?->getId(),
                                $unit?->getId(),
                                4
                            );
                    }
                }
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
     * method to fetch pejabat yang berwenang (Generic)
     * @param JabatanPegawai $jabatanPegawai
     * @return array
     */
    public function getPybFromJabatanPegawai(JabatanPegawai $jabatanPegawai): array
    {
        $jabatan        = $jabatanPegawai->getJabatan();
        $kantor         = $jabatanPegawai->getKantor();
        $unit           = $jabatanPegawai->getUnit();
        $levelUnit      = $unit?->getLevel();
        $jenisJabatan   = $jabatan?->getJenis();
        $levelJabatan   = $jabatan?->getLevel();
        $tipeKantor     = $kantor?->getJenisKantor()?->getTipe();

        $jabatanPegawaiPyb = null;

        // Get the parent Unit & Kantor data
        $parentUnit     = $unit?->getParent();
        $parentKantor   = $kantor?->getParent();
        $kantorPembina  = $kantor?->getPembina();
        $unitPembina    = $unit?->getPembina();
        $parentNama     = $parentKantor?->getNama();

        if(isset($kantorPembina)){$parentKantor = $kantorPembina;}
        if(isset($unitPembina)){$parentUnit = $unitPembina;}

        $parentUnitLv2  = $parentUnit?->getParent();

        if ('STRUKTURAL' === $jenisJabatan) {
            // Define the atasan level based on current level
            if (0 >= $levelJabatan) {
                $tingkatEselonPyb = 0;
            } elseif (6 === $levelJabatan) {
                $tingkatEselonPyb = 3;
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
                    ) &&  !in_array(
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
                //For echelon 4 at Kanwil and Directorate, but not below setditjen
                if (4 === $levelJabatan
                     &&  !in_array(
                        $parentNama,
                        self::SETDITJEN,
                        true
                    )
                ) {
                    $jabatanPegawaiPyb = self::getKepalaKantorFromKantor($kantor);
                    // The others, atasan is at parent kantor
                } else {
                    $jabatanPegawaiPyb = self::getKepalaKantorFromKantor($parentKantor);
                }
            } else {
                if (!in_array(
                        $tipeKantor,
                        self::TIPE_KANTOR_KP2KP,
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
                }else{
                    $jabatanPegawaiPyb = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $parentKantor?->getId(),
                            $parentUnit?->getId(),
                            $tingkatEselonPyb
                        );
                }
            }

            // For the fungsional
        } elseif ('FUNGSIONAL' === $jenisJabatan) {
            $groupJabatanName = $jabatan?->getGroupJabatan()?->getNama();

            // For fungsional under kepala kantor
            if (4 !== $levelUnit)
            {
                $jabatanPegawaiPyb = self::getKepalaKantorFromKantor($kantor);
            } else {
                if (!in_array(
                    $tipeKantor,
                    self::TIPE_KANTOR_KP2KP,
                    true
                    )
                ) {
                    $jabatanPegawaiPyb = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $kantor?->getId(),
                            $parentUnit?->getId(),
                            3
                        );
                }else{
                    $jabatanPegawaiPyb = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $parentKantor?->getId(),
                            $parentUnit?->getId(),
                            3
                        );
                }
            }
        }

        // Make sure the output is instanceof JabatanPegawai
        if ($jabatanPegawaiPyb instanceof JabatanPegawai) {
            return $this->makeOutputSinglePegawaiFromJabatanPegawai($jabatanPegawaiPyb);
        }

        return ['No Pyb found.'];
    }

    /**
     * method to fetch Pejabat Yang Berwenang Cuti Sakit Lebih Dari 14 Hari,
     * Cuti Besar, CAP, Melahirkan
     * @param JabatanPegawai $jabatanPegawai
     * @return array
     */
    public function getPybCutiDiaturFromJabatanPegawai(JabatanPegawai $jabatanPegawai): array
    {
        $jabatan        = $jabatanPegawai->getJabatan();
        $kantor         = $jabatanPegawai->getKantor();
        $unit           = $jabatanPegawai->getUnit();
        $pegawai        = $jabatanPegawai->getPegawai();
        $levelUnit      = $unit?->getLevel();
        $jenisJabatan   = $jabatan?->getJenis();
        $levelJabatan   = $jabatan?->getLevel();
        $tipeKantor     = $kantor?->getJenisKantor()?->getTipe();
        $pangkat        = $pegawai?->getPangkat();

        $jabatanPegawaiPyb = null;

        // Get the parent Unit & Kantor data
        $parentUnit     = $unit?->getParent();
        $parentKantor   = $kantor?->getParent();
        $kantorPembina  = $kantor?->getPembina();
        $unitPembina    = $unit?->getPembina();
        $parentNama     = $parentKantor?->getNama();

        if(isset($kantorPembina)){$parentKantor = $kantorPembina;}
        if(isset($unitPembina)){$parentUnit = $unitPembina;}

        $parentUnitLv2  = $parentUnit?->getParent();

        if ('STRUKTURAL' === $jenisJabatan) {
            // Define the atasan level based on current level
            if (0 >= $levelJabatan) {
                $tingkatEselonPyb = 0;
            } elseif (6 === $levelJabatan) {
                $tingkatEselonPyb = 3;
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
                    ) &&  !in_array(
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
                if(
                    in_array(
                    $tipeKantor,
                    self::TIPE_KANTOR_WITH_SAME_ATASAN_KANTOR_FOR_ECHELON_THREE,
                    true
                    ) &&  !in_array(
                        $parentNama,
                        self::SETDITJEN,
                        true
                    )
                ){
                    $jabatanPegawaiPyb = self::getKepalaKantorFromKantor($kantor);
                }else{
                    $jabatanPegawaiPyb = self::getKepalaKantorFromKantor($parentKantor);
                }
            } else {
                if (!in_array(
                    $tipeKantor,
                    self::TIPE_KANTOR_KP2KP,
                    true
                    )
                ) {
                    if(in_array(
                        $parentNama,
                        self::SETDITJEN,
                        true
                        )
                    ){
                        $jabatanPegawaiPyb = $this->entityManager
                            ->getRepository(JabatanPegawai::class)
                            ->findJabatanPegawaiKabagP4();
                    }else if(
                        in_array(
                            $tipeKantor,
                            self::TIPE_KANTOR_WITH_SAME_ATASAN_KANTOR_FOR_ECHELON_THREE,
                            true
                        )
                    ){
                        $jabatanPegawaiPyb = $this->entityManager
                            ->getRepository(JabatanPegawai::class)
                            ->findJabatanPegawaiActiveFromKantorUnitEselon(
                                $kantor?->getId(),
                                $parentUnit?->getId(),
                                $tingkatEselonPyb
                            );
                    }else{
                        $jabatanPegawaiPyb = self::getKepalaKantorFromKantor($kantor);
                    }
                }else{
                    $jabatanPegawaiPyb = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $parentKantor?->getId(),
                            $parentUnit?->getId(),
                            $tingkatEselonPyb
                        );
                }
            }
        // For the fungsional
        } elseif ('FUNGSIONAL' === $jenisJabatan) {
            $groupJabatanName = $jabatan?->getGroupJabatan()?->getNama();


            // For fungsional under kepala kantor
            if (4 !== $levelUnit && 'KANWIL' !== $tipeKantor)
            {
                $jabatanPegawaiPyb = self::getKepalaKantorFromKantor($kantor);
            } else {
                if (!in_array(
                    $tipeKantor,
                    self::TIPE_KANTOR_WITH_SAME_ATASAN_KANTOR_FOR_ECHELON_THREE,
                    true
                )
                ) {
                    if(preg_match("/IV/i", $pangkat)){
                        $jabatanPegawaiPyb = self::getKepalaKantorFromKantor($kantor);
                    }else{
                        $jabatanPegawaiPyb = $this->entityManager
                            ->getRepository(JabatanPegawai::class)
                            ->findKabagUmumKanwilFromKantorEselon(
                                $kantor?->getId(),
                                3
                        );
                    }
                }else{
                    $jabatanPegawaiPyb = $this->entityManager
                        ->getRepository(JabatanPegawai::class)
                        ->findJabatanPegawaiActiveFromKantorUnitEselon(
                            $parentKantor?->getId(),
                            $parentUnit?->getId(),
                            3
                    );
                }
            }
        }

        // Make sure the output is instanceof JabatanPegawai
        if ($jabatanPegawaiPyb instanceof JabatanPegawai) {
            return $this->makeOutputSinglePegawaiFromJabatanPegawai($jabatanPegawaiPyb);
        }

        return ['No Pyb found.'];
    }

    /**
     * method to fetch Pejabat Yang Berwenang Cuti Sakit Lebih Dari 14 Hari,
     * Cuti Besar, CAP, Melahirkan
     * @param JabatanPegawai $jabatanPegawai
     * @return array
     */
    public function getPybIzinDiaturFromJabatanPegawai(JabatanPegawai $jabatanPegawai): array
    {
        $jabatan        = $jabatanPegawai->getJabatan();
        $kantor         = $jabatanPegawai->getKantor();
        $unit           = $jabatanPegawai->getUnit();
        $pegawai        = $jabatanPegawai->getPegawai();
        $levelUnit      = $unit?->getLevel();
        $namaJabatan    = $jabatan?->getNama();
        $jenisJabatan   = $jabatan?->getJenis();
        $levelJabatan   = $jabatan?->getLevel();
        $tipeKantor     = $kantor?->getJenisKantor()?->getTipe();
        $pangkat        = $pegawai?->getPangkat();

        $jabatanPegawaiPyb = null;

        // Get the parent Unit & Kantor data
        $parentUnit     = $unit?->getParent();
        $parentKantor   = $kantor?->getParent();
        $kantorPembina  = $kantor?->getPembina();
        $unitPembina    = $unit?->getPembina();
        $parentNama     = $parentKantor?->getNama();

        if(isset($kantorPembina)){$parentKantor = $kantorPembina;}
        if(isset($unitPembina)){$parentUnit = $unitPembina;}

        $parentUnitLv2  = $parentUnit?->getParent();

        if ('STRUKTURAL' === $jenisJabatan) {
            // Define the atasan level based on current level
            if (0 >= $levelJabatan) {
                $tingkatEselonPyb = 0;
            } elseif (6 === $levelJabatan) {
                $tingkatEselonPyb = 4;
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
                    ) &&  !in_array(
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
                if(
                    in_array(
                        $tipeKantor,
                        self::TIPE_KANTOR_WITH_SAME_ATASAN_KANTOR_FOR_ECHELON_THREE,
                        true
                    ) &&  !in_array(
                        $parentNama,
                        self::SETDITJEN,
                        true
                    )
                ){
                    if (!in_array(
                        $tipeKantor,
                        self::TIPE_KANTOR_KP2KP,
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
                    }else{
                        $jabatanPegawaiPyb = $this->entityManager
                            ->getRepository(JabatanPegawai::class)
                            ->findJabatanPegawaiActiveFromKantorUnitEselon(
                                $parentKantor?->getId(),
                                $parentUnit?->getId(),
                                $tingkatEselonPyb
                            );
                    }
                }else{
                    $jabatanPegawaiPyb = self::getKepalaKantorFromKantor($parentKantor);
                }
            } else {
                $jabatanPegawaiPyb = $this->entityManager
                    ->getRepository(JabatanPegawai::class)
                    ->findJabatanPegawaiActiveFromKantorUnitEselon(
                        $kantor?->getId(),
                        $unit?->getId(),
                        $tingkatEselonPyb
                    );
            }
            // For the fungsional
        } elseif ('FUNGSIONAL' === $jenisJabatan) {
            $groupJabatanName = $jabatan?->getGroupJabatan()?->getNama();


            // For fungsional under kepala kantor
            if (4 !== $levelUnit)
            {
                $jabatanPegawaiPyb = self::getKepalaKantorFromKantor($kantor);
            } else {
                if (in_array(
                        $tipeKantor,
                        self::TIPE_KANTOR_KP2KP,
                        true
                    )
                ) {
                    if(
                        preg_match("/Madya/i", $namaJabatan)
                        || preg_match("/Utama/i", $namaJabatan)
                    ) {
                        $jabatanPegawaiPyb = $this->entityManager
                            ->getRepository(JabatanPegawai::class)
                            ->findJabatanPegawaiDirjen();
                    }else if(
                        preg_match("/Muda/i", $namaJabatan)
                        || preg_match("/Penyelia/i", $namaJabatan)
                    ){
                        $jabatanPegawaiPyb = $this->entityManager
                            ->getRepository(JabatanPegawai::class)
                            ->findJabatanPegawaiActiveFromKantorUnitEselon(
                                $kantor?->getId(),
                                $parentUnit?->getId(),
                                3
                            );
                    }else{
                        $jabatanPegawaiPyb = $this->entityManager
                            ->getRepository(JabatanPegawai::class)
                            ->findJabatanPegawaiActiveFromKantorUnitEselon(
                                $kantor?->getId(),
                                $unit?->getId(),
                                4
                            );
                    }
                }else{ // from KP2KP
                    if(
                        preg_match("/Muda/i", $namaJabatan)
                        || preg_match("/Penyelia/i", $namaJabatan)
                    ){
                        $jabatanPegawaiPyb = $this->entityManager
                            ->getRepository(JabatanPegawai::class)
                            ->findJabatanPegawaiActiveFromKantorUnitEselon(
                                $parentKantor?->getId(),
                                $parentUnit?->getId(),
                                3
                            );
                    }else{
                        $jabatanPegawaiPyb = $this->entityManager
                            ->getRepository(JabatanPegawai::class)
                            ->findJabatanPegawaiActiveFromKantorUnitEselon(
                                $kantor?->getId(),
                                $unit?->getId(),
                                4
                            );
                    }
                }
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
