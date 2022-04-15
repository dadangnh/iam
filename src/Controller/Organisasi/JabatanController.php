<?php

namespace App\Controller\Organisasi;

use App\Entity\Organisasi\Jabatan;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param String $name
     * @return JsonResponse
     */
    #[Route('/api/jabatans/active/{name}', methods: ['GET'])]
    public function getActiveJabatanByKeyword(ManagerRegistry $doctrine, String $name): JsonResponse
    {
        if (3 > strlen($name)) {
            return $this->json([
                'code' => 406,
                'error' => 'Please use 3 char or more for keyword'
            ], 406);
        }

        $jabatans = $doctrine
            ->getRepository(Jabatan::class)
            ->findActiveJabatanByNameKeyword($name);

        return $this->formatReturnData($jabatans);
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
            ], 404);
        }

        return $this->json([
            'jabatan_count' => count($jabatans),
            'jabatans' => $jabatans,
        ]);
    }
}
