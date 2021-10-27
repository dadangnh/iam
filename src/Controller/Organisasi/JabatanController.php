<?php

namespace App\Controller\Organisasi;

use App\Entity\Organisasi\Jabatan;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Restrict access to this controller only for user
 * @Security("is_granted('ROLE_USER')")
 */
class JabatanController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    #[Route('/api/jabatans/active/show_all', methods: ['GET'])]
    public function getAllActiveJabatan(): JsonResponse
    {
        $jabatans = $this->getDoctrine()
            ->getRepository(Jabatan::class)
            ->findAllActiveJabatan();

        return $this->formatReturnData($jabatans);
    }

    /**
     * @param String $name
     * @return JsonResponse
     */
    #[Route('/api/jabatans/active/{name}', methods: ['GET'])]
    public function getActiveJabatanByKeyword(String $name): JsonResponse
    {
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
}
