<?php

namespace App\Controller\Organisasi;

use App\Entity\Organisasi\Unit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Restrict access to this controller only for user
 * @Security("is_granted('ROLE_USER')")
 */
class UnitController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    #[Route('/api/units/active/show_all', methods: ['GET'])]
    public function getAllActiveUnit(): JsonResponse
    {
        $units = $this->getDoctrine()
            ->getRepository(Unit::class)
            ->findAllActiveUnit();

        return $this->formatReturnData($units);
    }

    /**
     * @param String $name
     * @return JsonResponse
     */
    #[Route('/api/units/active/{name}', methods: ['GET'])]
    public function getActiveUnitByKeyword(String $name): JsonResponse
    {
        if (3 > strlen($name)) {
            return $this->json([
                'code' => 406,
                'error' => 'Please use 3 char or more for keyword'
            ], 406);
        }

        $units = $this->getDoctrine()
            ->getRepository(Unit::class)
            ->findActiveUnitByNameKeyword($name);

        return $this->formatReturnData($units);
    }

    /**
     * @param array|null $units
     * @return JsonResponse
     */
    private function formatReturnData(?array $units): JsonResponse
    {
        if (empty($units)) {
            return $this->json([
                'code' => 404,
                'error' => 'No unit associated with this name'
            ], 404);
        }

        return $this->json([
            'unit_count' => count($units),
            'units' => $units,
        ]);
    }
}
