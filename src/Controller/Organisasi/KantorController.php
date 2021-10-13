<?php

namespace App\Controller\Organisasi;

use App\Entity\Organisasi\Kantor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class KantorController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    #[Route('/api/kantors/active/show_all', methods: ['GET'])]
    public function getAllActiveKantor(): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $kantors = $this->getDoctrine()
            ->getRepository(Kantor::class)
            ->findAllActiveKantor();

        return $this->formatReturnData($kantors);
    }

    #[Route('/api/kantors/active/{name}', methods: ['GET'])]
    public function getActiveKantorByKeyword(String $name): JsonResponse
    {
        $this->ensureUserLoggedIn();

        if (3 > strlen($name)) {
            return $this->json([
                'code' => 406,
                'error' => 'Please use 3 char or more for keyword',
                'name' => $name,
            ], 406);
        }

        $kantors = $this->getDoctrine()
            ->getRepository(Kantor::class)
            ->findActiveKantorByNameKeyword($name);

        return $this->formatReturnData($kantors);
    }

    /**
     * @param array|null $kantors
     * @return JsonResponse
     */
    private function formatReturnData(?array $kantors): JsonResponse
    {
        if (empty($kantors)) {
            return $this->json([
                'code' => 404,
                'error' => 'No kantor associated with this name'
            ], 404);
        }

        return $this->json([
            'kantor_count' => count($kantors),
            'kantors' => $kantors,
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
