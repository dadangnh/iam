<?php

namespace App\Controller\Pegawai;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Pegawai\Pegawai;
use App\Helper\PosisiHelper;
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
}
