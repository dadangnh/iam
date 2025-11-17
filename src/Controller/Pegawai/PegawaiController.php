<?php

namespace App\Controller\Pegawai;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Pegawai\JabatanPegawai;
use App\Entity\Pegawai\Pegawai;
use App\Entity\User\User;
use App\Helper\PosisiHelper;
use App\Helper\RoleHelper;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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
            ], 400);
        }

        // Make sure the provided data is valid
        $pegawaiId = $requestData['pegawaiId'];
        if (empty($pegawaiId) || is_array($pegawaiId) || is_bool($pegawaiId) || !Uuid::isValid($pegawaiId)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the valid uuid of Pegawai Entity.'
            ], 400);
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
            ], 200);
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
                //get only jabatan definitif
                if('e0be5909-c3b0-4fac-902e-963ba397e7e5' == $jabatanPegawai->getTipe()->getId()){
                    $output['jabatanPegawais'] = [
                        'iri'           => $iriConverter->getIriFromResource($jabatanPegawai),
                        'jabatanName'   => $jabatanPegawai->getJabatan()?->getNama(),
                        'kantorName'    => $jabatanPegawai->getKantor()?->getNama(),
                        'unitName'      => $jabatanPegawai->getUnit()?->getNama(),
                        'atasan'        => $posisiUtils->getAtasanFromJabatanPegawai($jabatanPegawai, null),
                        'atasanCuti'    => $posisiUtils->getAtasanFromJabatanPegawai($jabatanPegawai, 'atasanCuti'),
                        'pyb'           => $posisiUtils->getPybFromJabatanPegawai($jabatanPegawai, null),
                        'pybCutiDiatur' => $posisiUtils->getPybFromJabatanPegawai($jabatanPegawai, 'pybCutiDiatur'),
                        'pybIzin'       => $posisiUtils->getPybFromJabatanPegawai($jabatanPegawai, 'pybIzin')
                    ];
                }

            // For the non active, provide status.
            } /*else {
                $output['jabatanPegawais'] = [
                    'iri'           => $iriConverter->getIriFromResource($jabatanPegawai),
                    'jabatanName'   => $jabatanPegawai->getJabatan()?->getNama(),
                    'kantorName'    => $jabatanPegawai->getKantor()?->getNama(),
                    'unitName'      => $jabatanPegawai->getUnit()?->getNama(),
                    'atasan'        => 'Current jabatan is inactive. Cannot fetch atasan data.',
                    'atasanCuti'    => 'Current jabatan is inactive. Cannot fetch atasan data.',
                    'pyb'           => 'Current jabatan is inactive. Cannot fetch pyb data.',
                    'pybCutiDiatur' => 'Current jabatan is inactive. Cannot fetch pyb cuti diatur data.',
                    'pybIzin'       => 'Current jabatan is inactive. Cannot fetch pyb Izin data.'
                ];
            }*/
        }

        if (empty($output['jabatanPegawais'])){
            $output['jabatanPegawais'] = ['There is no Jabatan Pegawai found with the associated id.'];
        }

        return $this->json($output);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param PosisiHelper $posisiUtils
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/pegawais/v2/atasan', methods: ['POST'])]
    public function getAtasanPegawaiV2FromPegawaiId(ManagerRegistry       $doctrine,
                                                  Request               $request,
                                                  PosisiHelper          $posisiUtils,
                                                  IriConverterInterface $iriConverter): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        // Make sure the pegawaiId parameter exists
        if (!array_key_exists('pegawaiId', $requestData)) {
            return $this->json([
                'status' => 'error',
                'code'   => 'BAD_REQUEST',
                'message'=> 'Please provide the uuid of Pegawai Entity inside pegawaiId parameter.',
                'data'   => [
                    'additionalInfo' => ['pegawaiId' => '5a3770f2-e461-4f79-93e9-801a0986725f']
                ]
            ], 400);
        }

        // Make sure the provided data is valid
        $pegawaiId = $requestData['pegawaiId'];
        if (empty($pegawaiId) || is_array($pegawaiId) || is_bool($pegawaiId) || !Uuid::isValid($pegawaiId)) {
            return $this->json([
                'status' => 'error',
                'code'   => 'BAD_REQUEST',
                'message'=> 'Please provide the valid uuid of Pegawai Entity.',
                'data'   => [
                    'additionalInfo' => $requestData
                ]
            ], 400);
        }

        // Fetch the pegawai data
        $pegawai = $doctrine
            ->getRepository(Pegawai::class)
            ->findOneBy(['id' => $pegawaiId]);

        // If no data found, return
        if (null === $pegawai) {
            return $this->json([
                'status' => 'fail',
                'code'   => 'DATA_NOT_FOUND',
                'message'=> 'There is no Pegawai found with the associated id.',
                'data'   => [
                    'additionalInfo' => $requestData
                ]
            ], 200);
        }

        // Set the default template
        $output = [
            'pegawaiId'     => $pegawaiId,
            'pegawaiName'   => $pegawai->getNama(),
            'nip9'          => $pegawai->getNip9(),
            'nip18'         => $pegawai->getNip18(),
        ];

        // Iterate the jabatan pegawai and fetch the atasan
        foreach ($pegawai->getJabatanPegawais() as $jabatanPegawai) {
            $today = new DateTimeImmutable('now');

            // Only process the active jabatan
            if ($today >= $jabatanPegawai->getTanggalMulai()
                && (null === $jabatanPegawai->getTanggalSelesai()
                    || $today <= $jabatanPegawai->getTanggalSelesai())
            ) {
                $output['jabatanPegawais'][] = [
                    'iri'           => $iriConverter->getIriFromResource($jabatanPegawai),
                    'jabatanName'   => $jabatanPegawai->getJabatan()?->getNama(),
                    'kantorName'    => $jabatanPegawai->getKantor()?->getNama(),
                    'unitName'      => $jabatanPegawai->getUnit()?->getNama(),
                    'atasan'        => $posisiUtils->getAtasanFromJabatanPegawai($jabatanPegawai, null),
                    'atasanCuti'    => $posisiUtils->getAtasanFromJabatanPegawai($jabatanPegawai, 'atasanCuti'),
                    'pyb'           => $posisiUtils->getPybFromJabatanPegawai($jabatanPegawai, null),
                    'pybCutiDiatur' => $posisiUtils->getPybFromJabatanPegawai($jabatanPegawai, 'pybCutiDiatur'),
                    'pybIzin'       => $posisiUtils->getPybFromJabatanPegawai($jabatanPegawai, 'pybIzin')
                ];
            }
        }

        if (empty($output['jabatanPegawais'])){
            $output['jabatanPegawais'] = ['There is no Jabatan Pegawai found with the associated id.'];
        }

        return $this->json([
            'status' => 'success',
            'code'   => 'DATA_FOUND',
            'message'=> 'Data ditemukan',
            'data'   => $output
        ]);
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
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/pegawais/info', methods: ['POST'])]
    public function getPegawaiInfoFromJabatanPegawaiId(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        // Make sure the pegawaiId parameter exists
        if (!array_key_exists('jabatanPegawaiId', $requestData)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the uuid of Jabatan Pegawai Entity inside jabatanPegawaiId parameter.'
            ], 400);
        }

        // Make sure the provided data is valid
        $jabatanPegawaiId = $requestData['jabatanPegawaiId'];
        if (empty($jabatanPegawaiId) || is_array($jabatanPegawaiId) || is_bool($jabatanPegawaiId) || !Uuid::isValid($jabatanPegawaiId)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the valid uuid of Jabatan Pegawai Entity.'
            ], 400);
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
            ], 200);
        }

        $levelUnit = $jabatanPegawai->getUnit()->getLevel();

        //get role by jabatan pegawai object
        $rolesJabatan   = RoleHelper::getPlainRolesNameFromJabatanPegawai(
                            $doctrine->getManager(),
                            $jabatanPegawai
                        );

        //get role by user id
        $rolesUser      = $jabatanPegawai->getPegawai()->getUser()->getDirectRoles();
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

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/pegawais/v2/info', methods: ['POST'])]
    public function getPegawaiInfoV2FromJabatanPegawaiId(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        // Make sure the pegawaiId parameter exists
        if (!array_key_exists('jabatanPegawaiId', $requestData)) {
            return $this->json([
                'status' => 'error',
                'code'   => 'BAD_REQUEST',
                'message'=> 'Please provide the uuid of Jabatan Pegawai Entity inside jabatanPegawaiId parameter.',
                'data'   => [
                    'additionalInfo' => ['jabatanPegawaiId' => '5a3770f2-e461-4f79-93e9-801a0986725f']
                ]
            ], 400);
        }

        // Make sure the provided data is valid
        $jabatanPegawaiId = $requestData['jabatanPegawaiId'];
        if (empty($jabatanPegawaiId) || is_array($jabatanPegawaiId) || is_bool($jabatanPegawaiId) || !Uuid::isValid($jabatanPegawaiId)) {
            return $this->json([
                'status' => 'error',
                'code'   => 'BAD_REQUEST',
                'message'=> 'Please provide the valid uuid of Jabatan Pegawai Entity.',
                'data'   => [
                    'additionalInfo' => ['jabatanPegawaiId' => '5a3770f2-e461-4f79-93e9-801a0986725f']
                ]
            ], 400);
        }

        // Fetch the pegawai data
        $jabatanPegawai = $doctrine
            ->getRepository(JabatanPegawai::class)
            ->findOneBy(['id' => $jabatanPegawaiId]);

        // If no data found, return
        if (null === $jabatanPegawai) {
            return $this->json([
                'status' => 'fail',
                'code'   => 'DATA_NOT_FOUND',
                'message'=> 'There is no Pegawai found with the associated id.',
                'data'   => [
                    'additionalInfo' => $requestData
                ]
            ], 200);
        }
        $levelUnit = $jabatanPegawai->getUnit()->getLevel();

        //get role by jabatan pegawai object
        $rolesJabatan   = RoleHelper::getPlainRolesNameFromJabatanPegawai(
                            $doctrine->getManager(),
                            $jabatanPegawai
                        );

        //get role by user id
        $rolesUser      = $jabatanPegawai->getPegawai()->getUser()->getDirectRoles();
        // merge role
        $rolesNya       = array_merge($rolesJabatan, $rolesUser);

        // Set the default template
        $output = [
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

        return $this->json([
            'status' => 'success',
            'code'   => 'DATA_FOUND',
            'message'=> 'Data ditemukan',
            'data'   => $output
        ]);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api-ext/pegawais/v1/info/from-token', methods: ['POST'])]
    public function getPegawaiInfoV2FromUserId(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $userNya = $this->getUser();
        // If no data found, return
        if (null === $userNya) {
            return $this->json([
                'status' => 'fail',
                'code'   => 'DATA_NOT_FOUND',
                'message'=> 'There is no User found with the associated id.',
                'data'   => [
                    'additionalInfo' => $userNya
                ]
            ], 200);
        }

        $output = [
            'pegawaiId'             => $userNya->getPegawai()->getId(),
            'nip9'                  => $userNya->getPegawai()->getNip9(),
            'nip18'                 => $userNya->getPegawai()->getNip18(),
            'nama'                  => $userNya->getPegawai()->getNama(),
            'pangkat'               => $userNya->getPegawai()->getPangkat(),
            'username'              => $userNya->getUsername()
        ];

        $getJabatanPegawai = $userNya->getPegawai()->getJabatanPegawais();

        // If no data found, return
        if (null === $getJabatanPegawai) {
            return $this->json([
                'status' => 'fail',
                'code'   => 'DATA_NOT_FOUND',
                'message'=> 'There is no Pegawai found with the associated id.',
                'data'   => [
                    'additionalInfo' => $userNya
                ]
            ], 200);
        }
        foreach ($getJabatanPegawai as $jabatanPegawai) {
            $levelUnit = $jabatanPegawai->getUnit()->getLevel();

            //get role by jabatan pegawai object
            $rolesJabatan   = RoleHelper::getPlainRolesNameFromJabatanPegawai(
                $doctrine->getManager(),
                $jabatanPegawai
            );

            //get role by user id
            $rolesUser      = $jabatanPegawai->getPegawai()->getUser()->getDirectRoles();
            // merge role
            $rolesNya       = array_merge($rolesJabatan, $rolesUser);

            // Set the default template
            $jp = [
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

            switch ($levelUnit) {
                case 2:
                    $jp += [
                        'unitEs4Id'         => null,
                        'unitEs4Name'       => null,
                        'unitEs4LegacyCode' => null,
                        'unitEs3Id'         => null,
                        'unitEs3Name'       => null,
                        'unitEs3LegacyCode' => null,
                    ];
                    $jp += $this->getUnitEsArray($jabatanPegawai->getUnit(),2);
                    break;

                case 3:
                    $jp += [
                        'unitEs4Id'         => null,
                        'unitEs4Name'       => null,
                        'unitEs4LegacyCode' => null,
                    ];
                    $jp += $this->getUnitEsArray($jabatanPegawai->getUnit(),3);
                    $parent = $jabatanPegawai->getUnit()->getParent();
                    $jp += ($parent && $parent->getLevel() == 2)
                        ? $this->getUnitEsArray($parent,2)
                        : ['unitEs2Id' => null, 'unitEs2Name' => null, 'unitEs2LegacyCode' => null];
                    break;

                case 4:
                    $unit   = $jabatanPegawai->getUnit();
                    $parent = $unit->getParent();
                    $jp += $this->getUnitEsArray($unit,4);

                    if ($parent && $parent->getLevel() == 3) {
                        $jp += $this->getUnitEsArray($parent,3);
                        $grandParent = $parent->getParent();
                        $jp += ($grandParent && $grandParent->getLevel() == 2)
                            ? $this->getUnitEsArray($grandParent,2)
                            : ['unitEs2Id' => null, 'unitEs2Name' => null, 'unitEs2LegacyCode' => null];
                    } else {
                        $jp += [
                            'unitEs3Id' => null,
                            'unitEs3Name' => null,
                            'unitEs3LegacyCode' => null,
                        ];
                        $jp += $this->getUnitEsArray($parent,2);
                    }
                    break;
            }

            $jp['roles'] = $rolesNya;
            $output['jabatanPegawai'][] = $jp;
        }

        return $this->json([
            'status' => 'success',
            'code'   => 'DATA_FOUND',
            'message'=> 'Data ditemukan',
            'data'   => $output
        ]);
    }

    private function getUnitEsArray($unit, $level): array {
        return [
            'unitEs'.$level.'Id'        => $unit->getId(),
            'unitEs'.$level.'Name'      => $unit->getNama(),
            'unitEs'.$level.'LegacyCode'=> $unit->getLegacyKode(),
        ];
    }
}
