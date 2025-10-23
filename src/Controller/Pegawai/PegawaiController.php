<?php

namespace App\Controller\Pegawai;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Pegawai\JabatanPegawai;
use App\Entity\Pegawai\Pegawai;
use App\Helper\PosisiHelper;
use App\Helper\RoleHelper;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\Pure;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

/**
 * Restrict access to this controller only for user
 */
#[IsGranted('ROLE_USER')]
class PegawaiController extends AbstractController
{
    /**
     * @throws JsonException
     */
    #[Route('/api/pegawais/mass_fetch', methods: ['POST'])]
    public function getPegawaiDataFromArrayOfUuid(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $requestIds = $requestData['pegawaiIds'];

        // Check whether the request ids is not null
        if (!is_array($requestIds) || empty($requestIds)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the uuid in an array format.'
            ], 204);
        }

        // Make sure only accept Uuid
        $invalidUuid = $listEntityUuid = [];
        foreach ($requestIds as $id) {
            if (!Uuid::isValid($id)) {
                $invalidUuid[] = $id;
            } else {
                $listEntityUuid[] = $id;
            }
        }

        // If contains invalid uuid, return
        if (!empty($invalidUuid)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the uuid in an array format.',
                'invalidUuids' => $invalidUuid
            ], 204);
        }

        // Get all the data
        $pegawais = $doctrine
            ->getRepository(Pegawai::class)
            ->getPegawaisFromArrayOfUuid($listEntityUuid);

        // If there is no data, return
        if (empty($pegawais)) {
            return $this->json([
                'code' => 200,
                'messages' => 'No Pegawais with provided ids found.'
            ]);
        }

        // Make ordered result as requested
        $formattedOutput = $this->orderedOutput($requestIds, $pegawais);
        return $this->json($formattedOutput);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param PosisiHelper $posisiUtils
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/pegawais/atasan', methods: ['POST'])]
    public function getAtasanPegawaiFromPegawaiId(ManagerRegistry       $doctrine,
                                                  Request               $request,
                                                  PosisiHelper          $posisiUtils,
                                                  IriConverterInterface $iriConverter): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        // Make sure the pegawaiId parameter exists
        if (!array_key_exists('pegawaiId', $requestData)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the uuid of Pegawai Entity inside pegawaiId parameter.'
            ], 204);
        }

        // Make sure the provided data is valid
        $pegawaiId = $requestData['pegawaiId'];
        if (empty($pegawaiId) || is_array($pegawaiId) || is_bool($pegawaiId) || !Uuid::isValid($pegawaiId)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the valid uuid of Pegawai Entity.'
            ], 204);
        }

        // Fetch the pegawai data
        $pegawai = $doctrine
            ->getRepository(Pegawai::class)
            ->findOneBy(['id' => $pegawaiId]);

        // If no data found, return
        if (null === $pegawai) {
            return $this->json([
                'code' => 404,
                'errors' => 'There is no Pegawai found with the associated id.'
            ], 204);
        }

        // Set the default template
        $output = [
            'code' => 200,
            'pegawaiId' => $pegawaiId,
            'pegawaiName' => $pegawai->getNama(),
            'nip9' => $pegawai->getNip9(),
            'nip18' => $pegawai->getNip18(),
        ];

        // Iterate the jabatan pegawai and fetch the atasan
        foreach ($pegawai->getJabatanPegawais() as $jabatanPegawai) {
            $today = new DateTimeImmutable('now');

            // Only process the active jabatan
            if ($today >= $jabatanPegawai->getTanggalMulai()
                && (null === $jabatanPegawai->getTanggalSelesai()
                    || $today <= $jabatanPegawai->getTanggalSelesai())
            ) {
                $output['jabatanPegawais'] = [
                    'iri' => $iriConverter->getIriFromResource($jabatanPegawai),
                    'jabatanName' => $jabatanPegawai->getJabatan()?->getNama(),
                    'kantorName' => $jabatanPegawai->getKantor()?->getNama(),
                    'unitName' => $jabatanPegawai->getUnit()?->getNama(),
                    'atasan' => $posisiUtils->getAtasanFromJabatanPegawai($jabatanPegawai, null),
                    'atasanCuti' => $posisiUtils->getAtasanFromJabatanPegawai($jabatanPegawai, 'atasanCuti'),
                    'pyb' => $posisiUtils->getPybFromJabatanPegawai($jabatanPegawai, null),
                    'pybCutiDiatur' => $posisiUtils->getPybFromJabatanPegawai($jabatanPegawai, 'pybCutiDiatur'),
                    'pybIzin' => $posisiUtils->getPybFromJabatanPegawai($jabatanPegawai, 'pybIzin')
                ];

            // For the non active, provide status.
            } else {
                $output['jabatanPegawais'] = [
                    'iri' => $iriConverter->getIriFromResource($jabatanPegawai),
                    'jabatanName' => $jabatanPegawai->getJabatan()?->getNama(),
                    'kantorName' => $jabatanPegawai->getKantor()?->getNama(),
                    'unitName' => $jabatanPegawai->getUnit()?->getNama(),
                    'atasan' => 'Current jabatan is inactive. Cannot fetch atasan data.',
                    'atasanCuti' => 'Current jabatan is inactive. Cannot fetch atasan data.',
                    'pyb' => 'Current jabatan is inactive. Cannot fetch pyb data.',
                    'pybCutiDiatur' => 'Current jabatan is inactive. Cannot fetch pyb cuti diatur data.',
                    'pybIzin' => 'Current jabatan is inactive. Cannot fetch pyb Izin data.'
                ];
            }
        }

        return $this->json($output);
    }

    /**
     * @param $requestIds
     * @param $pegawais
     * @return array
     */
    #[Pure] private function orderedOutput($requestIds, $pegawais): array
    {
        $result = $orderedResult = [];

        /** @var Pegawai $pegawai */
        foreach ($pegawais as $pegawai) {
            $result[(string) $pegawai->getId()] = $pegawai;
        }

        foreach ($requestIds as $id) {
            if (!array_key_exists($id, $result)) {
                $result[$id] = null;
            }
        }

        foreach ($requestIds as $id) {
            $orderedResult[$id] = $result[$id];
        }

        return $orderedResult;
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/pegawais/info', methods: ['POST'])]
    public function getPegawaiInfoFromJabatanPegawaiId(ManagerRegistry       $doctrine,
                                                  Request                   $request,
                                                  IriConverterInterface $iriConverter): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        // Make sure the pegawaiId parameter exists
        if (!array_key_exists('jabatanPegawaiId', $requestData)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the uuid of Jabatan Pegawai Entity inside jabatanPegawaiId parameter.'
            ], 204);
        }

        // Make sure the provided data is valid
        $jabatanPegawaiId = $requestData['jabatanPegawaiId'];
        if (empty($jabatanPegawaiId) || is_array($jabatanPegawaiId) || is_bool($jabatanPegawaiId) || !Uuid::isValid($jabatanPegawaiId)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the valid uuid of Pegawai Entity.'
            ], 204);
        }

        // Fetch the pegawai data
        $jabatanPegawai = $doctrine
            ->getRepository(JabatanPegawai::class)
            ->findOneBy(['id' => $jabatanPegawaiId]);

        // If no data found, return
        if (null === $jabatanPegawai) {
            return $this->json([
                'code' => 404,
                'errors' => 'There is no Pegawai found with the associated id.'
            ], 204);
        }

        $levelUnit = $jabatanPegawai->getUnit()->getLevel();

        //get role by jabatan pegawai object
        $rolesJabatan   = RoleHelper::getPlainRolesNameFromJabatanPegawai(
                            $doctrine->getManager(),
                            $jabatanPegawai
                        );

        //get role by user id
        $rolesUser      = $jabatanPegawai->getPegawai()->getUser()->getRoles();
        // merge role
        $rolesNya       = array_merge($rolesJabatan, $rolesUser);

        // Set the default template
        $output = [
            'code'              => 200,
            'pegawaiId'             => $jabatanPegawai->getPegawai()->getId(),
            'nip9'                  => $jabatanPegawai->getPegawai()->getNip9(),
            'nip18'                 => $jabatanPegawai->getPegawai()->getNip18(),
            'nama'                  => $jabatanPegawai->getPegawai()->getNama(),
            'pangkat'               => $jabatanPegawai->getPegawai()->getPangkat(),
            'username'              => $jabatanPegawai->getPegawai()->getUser()->getUsername(),
            'jabatanId'             => $jabatanPegawai->getJabatan()->getId(),
            'jabatanName'           => $jabatanPegawai->getJabatan()->getNama(),
            'jenisJabatan'          => $jabatanPegawai->getJabatan()->getJenis(),
            'jabatanLegacyCode'     => $jabatanPegawai->getJabatan()->getLegacyKode(),
            'jabatanType'           => $jabatanPegawai->getTipe()->getNama(),
            'levelJabatan'          => $jabatanPegawai->getJabatan()->getLevel(),
            'kantorId'              => $jabatanPegawai->getKantor()->getId(),
            'kantorName'            => $jabatanPegawai->getKantor()->getNama(),
            'kantorLegacyCode'      => $jabatanPegawai->getKantor()->getLegacyKode(),
            'levelKantor'           => $jabatanPegawai->getKantor()->getLevel(),
            'kantorParentId'        => $jabatanPegawai->getKantor()->getParent()->getId(),
            'kantorParentName'      => $jabatanPegawai->getKantor()->getParent()->getNama(),
            'kantorParentLegacyCode'=> $jabatanPegawai->getKantor()->getParent()->getLegacyKode(),
            'levelKantorParent'     => $jabatanPegawai->getKantor()->getParent()->getLevel(),
            'legacyKodeKpp'         => $jabatanPegawai->getKantor()->getLegacyKodeKpp(),
            'legacyKodeKanwil'      => $jabatanPegawai->getKantor()->getLegacyKodeKanwil(),
            'jenisKantorId'         => $jabatanPegawai->getKantor()->getJenisKantor()->getId(),
            'jenisKantorName'       => $jabatanPegawai->getKantor()->getJenisKantor()->getNama(),
            'jenisKantorType'       => $jabatanPegawai->getKantor()->getJenisKantor()->getTipe(),
            'klasifikasiKantor'     => $jabatanPegawai->getKantor()->getJenisKantor()->getKlasifikasi(),
            'unitId'                => $jabatanPegawai->getUnit()->getId(),
            'unitName'              => $jabatanPegawai->getUnit()->getNama(),
            'unitLegacyCode'        => $jabatanPegawai->getUnit()->getLegacyKode(),
            'levelUnit'             => $levelUnit,
            'potitionInformation'   => $jabatanPegawai->getKeteranganJabatan(),
        ];

        /*if(2 == $levelUnit){
            $output['unitEs4Id']            = null;
            $output['unitEs4Name']          = null;
            $output['unitEs4LegacyCode']    = null;
            $output['unitEs3Id']            = null;
            $output['unitEs3Name']          = null;
            $output['unitEs3LegacyCode']    = null;
            $output['unitEs2Id']            = $jabatanPegawai->getUnit()->getId();
            $output['unitEs2Name']          = $jabatanPegawai->getUnit()->getNama();
            $output['unitEs2LegacyCode']    = $jabatanPegawai->getUnit()->getLegacyKode();
        }else if(3 == $levelUnit){
            $output['unitEs4Id']            = null;
            $output['unitEs4Name']          = null;
            $output['unitEs4LegacyCode']    = null;
            $output['unitEs3Id']            = $jabatanPegawai->getUnit()->getId();
            $output['unitEs3Name']          = $jabatanPegawai->getUnit()->getNama();
            $output['unitEs3LegacyCode']    = $jabatanPegawai->getUnit()->getLegacyKode();
            if(2 == $jabatanPegawai->getUnit()->getParent()->getLevel()){
                $output['unitEs2Id']            = $jabatanPegawai->getUnit()->getParent()->getId();
                $output['unitEs2Name']          = $jabatanPegawai->getUnit()->getParent()->getNama();
                $output['unitEs2LegacyCode']    = $jabatanPegawai->getUnit()->getParent()->getLegacyKode();
            } else {
                $output['unitEs2Id']            = null;
                $output['unitEs2Name']          = null;
                $output['unitEs2LegacyCode']    = null;
            }
        }else if(4 == $levelUnit){
            $output['unitEs4Id']            = $jabatanPegawai->getUnit()->getId();
            $output['unitEs4Name']          = $jabatanPegawai->getUnit()->getNama();
            $output['unitEs4LegacyCode']    = $jabatanPegawai->getUnit()->getLegacyKode();
            if(3 == $jabatanPegawai->getUnit()->getParent()->getLevel()) {
                $output['unitEs3Id']            = $jabatanPegawai->getUnit()->getParent()->getId();
                $output['unitEs3Name']          = $jabatanPegawai->getUnit()->getParent()->getNama();
                $output['unitEs3LegacyCode']    = $jabatanPegawai->getUnit()->getParent()->getLegacyKode();
                if(2 == $jabatanPegawai->getUnit()->getParent()->getParent()->getLevel()){
                    $output['unitEs2Id']            = $jabatanPegawai->getUnit()->getParent()->getParent()->getId();
                    $output['unitEs2Name']          = $jabatanPegawai->getUnit()->getParent()->getParent()->getNama();
                    $output['unitEs2LegacyCode']    = $jabatanPegawai->getUnit()->getParent()->getParent()->getLegacyKode();
                } else {
                    $output['unitEs2Id']            = null;
                    $output['unitEs2Name']          = null;
                    $output['unitEs2LegacyCode']    = null;
                }
            } else {
                $output['unitEs3Id']            = null;
                $output['unitEs3Name']          = null;
                $output['unitEs3LegacyCode']    = null;
                $output['unitEs2Id']            = $jabatanPegawai->getUnit()->getParent()->getId();
                $output['unitEs2Name']          = $jabatanPegawai->getUnit()->getParent()->getNama();
                $output['unitEs2LegacyCode']    = $jabatanPegawai->getUnit()->getParent()->getLegacyKode();
            }
        }*/

        switch ($levelUnit) {
            case 2:
                $output += [
                    'unitEs4Id'         => null,
                    'unitEs4Name'       => null,
                    'unitEs4LegacyCode' => null,
                    'unitEs3Id'         => null,
                    'unitEs3Name'       => null,
                    'unitEs3LegacyCode' => null,
                ];
                $output += $this->getUnitEsArray($jabatanPegawai->getUnit(),2);
                break;

            case 3:
                $output += [
                    'unitEs4Id'         => null,
                    'unitEs4Name'       => null,
                    'unitEs4LegacyCode' => null,
                ];
                $output += $this->getUnitEsArray($jabatanPegawai->getUnit(),3);
                $parent = $jabatanPegawai->getUnit()->getParent();
                $output += ($parent && $parent->getLevel() == 2)
                    ? $this->getUnitEsArray($parent,2)
                    : ['unitEs2Id' => null, 'unitEs2Name' => null, 'unitEs2LegacyCode' => null];
                break;

            case 4:
                $unit   = $jabatanPegawai->getUnit();
                $parent = $unit->getParent();
                $output += $this->getUnitEsArray($unit,4);

                if ($parent && $parent->getLevel() == 3) {
                    $output += $this->getUnitEsArray($parent,3);
                    $grandParent = $parent->getParent();
                    $output += ($grandParent && $grandParent->getLevel() == 2)
                        ? $this->getUnitEsArray($grandParent,2)
                        : ['unitEs2Id' => null, 'unitEs2Name' => null, 'unitEs2LegacyCode' => null];
                } else {
                    $output += [
                        'unitEs3Id' => null,
                        'unitEs3Name' => null,
                        'unitEs3LegacyCode' => null,
                    ];
                    $output += $this->getUnitEsArray($parent,2);
                }
                break;
        }

        $output['roles'] = $rolesNya;

        return $this->json($output);
    }

    private function getUnitEsArray($unit, $level): array {
        return [
            'unitEs'.$level.'Id'        => $unit->getId(),
            'unitEs'.$level.'Name'      => $unit->getNama(),
            'unitEs'.$level.'LegacyCode'=> $unit->getLegacyKode(),
        ];
    }
}
