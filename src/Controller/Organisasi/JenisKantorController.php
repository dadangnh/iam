<?php

namespace App\Controller\Organisasi;

use App\Entity\Organisasi\JenisKantor;
use App\Entity\Organisasi\Kantor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class JenisKantorController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    #[Route('/api/jenis_kantors/active/show_all', methods: ['GET'])]
    public function getAllActiveJenisKantor(): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $jenisKantors = $this->getDoctrine()
            ->getRepository(JenisKantor::class)
            ->findAllActiveJenisKantor();

        return $this->formatReturnData($jenisKantors);
    }

    #[Route('/api/jenis_kantors/active/{name}', methods: ['GET'])]
    public function getActiveJenisKantorByKeyword(String $name): JsonResponse
    {
        $this->ensureUserLoggedIn();

        if (3 > strlen($name)) {
            return $this->json([
                'code' => 406,
                'error' => 'Please use 3 char or more for keyword'
            ], 406);
        }

        $jenisKantors = $this->getDoctrine()
            ->getRepository(JenisKantor::class)
            ->findActiveJenisKantorByNameKeyword($name);

        return $this->formatReturnData($jenisKantors);
    }

    /**
     * @param array|null $jenisKantors
     * @return JsonResponse
     */
    private function formatReturnData(?array $jenisKantors): JsonResponse
    {
        if (empty($jenisKantors)) {
            return $this->json([
                'code' => 404,
                'error' => 'No jenis kantor associated with this name'
            ], 404);
        }

        return $this->json([
            'jenis_kantor_count' => count($jenisKantors),
            'jenis_kantors' => $jenisKantors,
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
