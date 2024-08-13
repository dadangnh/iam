<?php

namespace App\Controller\Organisasi;

use App\Entity\Organisasi\Jabatan;
use App\Entity\Pegawai\JabatanPegawai;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Restrict access to this controller only for user
 */
#[IsGranted('ROLE_USER')]
class JabatanController extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/api/jabatans/active/show_all', methods: ['GET'])]
    public function getAllActiveJabatan(ManagerRegistry $doctrine): JsonResponse
    {
        $jabatans = $doctrine
            ->getRepository(Jabatan::class)
            ->findAllActiveJabatan();

        return $this->formatReturnData($jabatans);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param string $name
     * @return JsonResponse
     */
    #[Route('/api/jabatans/active/{name}', methods: ['GET'])]
    public function getActiveJabatanByKeyword(ManagerRegistry $doctrine, string $name): JsonResponse
    {
        if (3 > strlen($name)) {
            return $this->json([
                'code' => 406,
                'error' => 'Please use 3 char or more for keyword'
            ], 204);
        }

        $jabatans = $doctrine
            ->getRepository(Jabatan::class)
            ->findActiveJabatanByNameKeyword($name);

        return $this->formatReturnData($jabatans);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/api/jabatan_pegawais/pegawai/{id}', methods: ['GET'])]
    public function getJabatanPegawaiByPegawaiId(ManagerRegistry $doctrine, string $id): JsonResponse
    {
        $jabatanPegawais = $doctrine
            ->getRepository(JabatanPegawai::class)
            ->findBy(['pegawai' => $id]);

        if (!$jabatanPegawais) {
            return new JsonResponse(['message' => 'No JabatanPegawai found for the given Pegawai ID'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Build the response array
        $response = [];

        foreach ($jabatanPegawais as $jabatanPegawai) {
            $response[] = [
                'id' => $jabatanPegawai->getId(),
                'jabatan' => [
                    'id' => $jabatanPegawai->getJabatan()->getId(),
                    'nama' => $jabatanPegawai->getJabatan()->getNama(),
                    'level' => $jabatanPegawai->getJabatan()->getLevel(),
                    'jenis' => $jabatanPegawai->getJabatan()->getJenis(),
                ],
                'tipe' => [
                    'id' => $jabatanPegawai->getTipe()->getId(),
                    'nama' => $jabatanPegawai->getTipe()->getNama(),
                ],
                'kantor' => [
                    'id' => $jabatanPegawai->getKantor()->getId(),
                    'nama' => $jabatanPegawai->getKantor()->getNama(),
                ],
                'unit' => [
                    'id' => $jabatanPegawai->getUnit()->getId(),
                    'nama' => $jabatanPegawai->getUnit()->getNama(),
                    'level' => $jabatanPegawai->getUnit()->getLevel(),
                ],
                'tanggalMulai' => $jabatanPegawai->getTanggalMulai()->format('Y-m-d\TH:i:sP'),

            ];
        }

        return new JsonResponse(['jabatanPegawais' => $response]);
    }

    /**
     * @param array|null $jabatans
     * @return JsonResponse
     */
    private function formatReturnData(?array $jabatans): JsonResponse
    {
        if (empty($jabatans)) {
            return $this->json([
                'code' => 404,
                'error' => 'No jabatan associated with this name'
            ], 204);
        }

        return $this->json([
            'jabatan_count' => count($jabatans),
            'jabatans' => $jabatans,
        ]);
    }
}
