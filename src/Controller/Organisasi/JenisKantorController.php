<?php

namespace App\Controller\Organisasi;

use App\Entity\Organisasi\JenisKantor;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Restrict access to this controller only for user
 * @Security("is_granted('ROLE_USER')")
 */
class JenisKantorController extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/api/jenis_kantors/active/show_all', methods: ['GET'])]
    public function getAllActiveJenisKantor(ManagerRegistry $doctrine): JsonResponse
    {
        $jenisKantors = $doctrine
            ->getRepository(JenisKantor::class)
            ->findAllActiveJenisKantor();

        return $this->formatReturnData($jenisKantors);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String $name
     * @return JsonResponse
     */
    #[Route('/api/jenis_kantors/active/{name}', methods: ['GET'])]
    public function getActiveJenisKantorByKeyword(ManagerRegistry $doctrine, String $name): JsonResponse
    {
        if (3 > strlen($name)) {
            return $this->json([
                'code' => 406,
                'error' => 'Please use 3 char or more for keyword'
            ], 406);
        }

        $jenisKantors = $doctrine
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
}
