<?php

namespace App\Controller\Organisasi;

use App\Entity\Organisasi\Jabatan;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class JabatanController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    #[Route('/api/jabatans/active/show_all', methods: ['GET'])]
    public function getAllActiveJabatan(): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $jabatans = $this->getDoctrine()
            ->getRepository(Jabatan::class)
            ->findAllActiveJabatan();

        return $this->formatReturnData($jabatans);
    }

    #[Route('/api/jabatans/active/{name}', methods: ['GET'])]
    public function getActiveJabatanByKeyword(String $name): JsonResponse
    {
        $this->ensureUserLoggedIn();

        if (3 > strlen($name)) {
            return $this->json([
                'code' => 406,
                'error' => 'Please use 3 char or more for keyword'
            ], 406);
        }

        $jabatans = $this->getDoctrine()
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

    /**
     * @return JsonResponse|null
     */
    private function ensureUserLoggedIn(): ?JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
        }

        return null;
    }
}
