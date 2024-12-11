<?php

namespace App\Controller\Organisasi;

use App\Entity\Organisasi\Unit;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Restrict access to this controller only for user
 */
#[IsGranted('ROLE_USER')]
class UnitController extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/api/units/active/show_all', methods: ['GET'])]
    public function getAllActiveUnit(ManagerRegistry $doctrine): JsonResponse
    {
        $units = $doctrine
            ->getRepository(Unit::class)
            ->findAllActiveUnitData();

        return $this->formatReturnData($units);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/api/units/active/get_all', methods: ['GET'])]
    public function getAllActiveUnitData(ManagerRegistry $doctrine): JsonResponse
    {
        $units = $doctrine
            ->getRepository(Unit::class)
            ->findAllActiveUnitData();

        return $this->formatReturnData($units);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param string $name
     * @return JsonResponse
     */
    #[Route('/api/units/active/{name}', methods: ['GET'])]
    public function getActiveUnitByKeyword(ManagerRegistry $doctrine, string $name): JsonResponse
    {
        if (3 > strlen($name)) {
            return $this->json([
                'code' => 406,
                'error' => 'Please use 3 char or more for keyword'
            ], 406);
        }

        $units = $doctrine
            ->getRepository(Unit::class)
            ->findActiveUnitByNameKeyword($name);

        return $this->formatReturnData($units);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/api/units/{id}/parent', methods: ['GET'])]
    #[Route('/api/units/find_parent/by_id/{id}', methods: ['GET'])]
    public function getParentUnitFromUnitId(ManagerRegistry $doctrine, string $id): JsonResponse
    {
        $unit = $doctrine
            ->getRepository(Unit::class)
            ->findOneBy(['id' => $id]);

        if (null === $unit) {
            return $this->json([
                'code' => 404,
                'errors' => 'There is no Unit found with the associated id.'
            ], 404);
        }

        return $this->processParentUnitData($unit);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param string $name
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[Route('/api/units/find_parent/by_exact_name/{name}', methods: ['GET'])]
    public function getParentUnitFromUnitName(ManagerRegistry $doctrine, string $name): JsonResponse
    {
        $unit = $doctrine
            ->getRepository(Unit::class)
            ->findActiveUnitByExactName($name);

        if (null === $unit) {
            return $this->json([
                'code' => 404,
                'errors' => 'There is no Unit found with the associated name.'
            ], 404);
        }

        return $this->processParentUnitData($unit);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param string $legacyKode
     * @return JsonResponse
     */
    #[Route('/api/units/find_parent/by_legacy_kode/{legacyKode}', methods: ['GET'])]
    public function getParentUnitFromUnitLegacyKode(ManagerRegistry $doctrine, string $legacyKode): JsonResponse
    {
        $unit = $doctrine
            ->getRepository(Unit::class)
            ->findOneBy(['legacyKode' => $legacyKode]);

        if (null === $unit) {
            return $this->json([
                'code' => 404,
                'errors' => 'There is no Unit found with the associated legacy kode.'
            ], 404);
        }

        return $this->processParentUnitData($unit);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/api/units/{id}/childs', methods: ['GET'])]
    #[Route('/api/units/find_childs/by_id/{id}', methods: ['GET'])]
    public function getChildUnitsFromUnitId(ManagerRegistry $doctrine, string $id): JsonResponse
    {
        $unit = $doctrine
            ->getRepository(Unit::class)
            ->findOneBy(['id' => $id]);

        if (null === $unit) {
            return $this->json([
                'code' => 404,
                'errors' => 'There is no Unit found with the associated id.'
            ], 404);
        }

        return $this->processChildUnitsData($unit);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param string $name
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    #[Route('/api/units/find_childs/by_exact_name/{name}', methods: ['GET'])]
    public function getChildUnitsFromUnitName(ManagerRegistry $doctrine, string $name): JsonResponse
    {
        $unit = $doctrine
            ->getRepository(Unit::class)
            ->findActiveUnitByExactName($name);

        if (null === $unit) {
            return $this->json([
                'code' => 404,
                'errors' => 'There is no Kantor found with the associated name.'
            ], 404);
        }

        return $this->processChildUnitsData($unit);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param string $legacyKode
     * @return JsonResponse
     */
    #[Route('/api/units/find_childs/by_legacy_kode/{legacyKode}', methods: ['GET'])]
    public function getChildUnitsFromUnitLegacyKode(ManagerRegistry $doctrine, string $legacyKode): JsonResponse
    {
        $unit = $doctrine
            ->getRepository(Unit::class)
            ->findOneBy(['legacyKode' => $legacyKode]);

        if (null === $unit) {
            return $this->json([
                'code' => 404,
                'errors' => 'There is no Unit found with the associated legacy kode.'
            ], 404);
        }

        return $this->processChildUnitsData($unit);
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

    /**
     * @param Unit $unit
     * @return JsonResponse
     */
    private function processParentUnitData(Unit $unit): JsonResponse
    {
        $parentUnit = $unit->getParent();

        if (null === $parentUnit) {
            return $this->json([
                'code' => 200,
                'id' => $unit->getId(),
                'nama' => $unit->getNama(),
                'level' => $unit->getLevel(),
                'parent' => null,
            ]);
        }

        return $this->json([
            'code' => 200,
            'id' => $unit->getId(),
            'nama' => $unit->getNama(),
            'level' => $unit->getLevel(),
            'parent' => $parentUnit,
        ]);
    }

    /**
     * @param Unit $unit
     * @return JsonResponse
     */
    private function processChildUnitsData(Unit $unit): JsonResponse
    {
        $childUnits = $unit->getChilds();

        if (0 === count($childUnits)) {
            return $this->json([
                'code' => 200,
                'id' => $unit->getId(),
                'nama' => $unit->getNama(),
                'level' => $unit->getLevel(),
                'childs' => null,
            ]);
        }

        return $this->json([
            'code' => 200,
            'id' => $unit->getId(),
            'nama' => $unit->getNama(),
            'level' => $unit->getLevel(),
            'childs' => $childUnits,
        ]);
    }
}
