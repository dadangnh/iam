<?php

namespace App\Controller\Organisasi;

use App\Entity\Organisasi\Kantor;
use App\Helper\PosisiHelper;
use Doctrine\Persistence\ManagerRegistry;
use JsonException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

/**
 * Restrict access to this controller only for user
 */
#[IsGranted('ROLE_USER')]
class KantorController extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/api/kantors/active/show_all', methods: ['GET'])]
    public function getAllActiveKantor(ManagerRegistry $doctrine): JsonResponse
    {
        $kantors = $doctrine
            ->getRepository(Kantor::class)
            ->findAllActiveKantor();

        return $this->formatReturnData($kantors);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param String $name
     * @return JsonResponse
     */
    #[Route('/api/kantors/active/{name}', methods: ['GET'])]
    public function getActiveKantorByKeyword(ManagerRegistry $doctrine, String $name): JsonResponse
    {
        if (3 > strlen($name)) {
            return $this->json([
                'code' => 406,
                'error' => 'Please use 3 char or more for keyword',
                'name' => $name,
            ], 406);
        }

        $kantors = $doctrine
            ->getRepository(Kantor::class)
            ->findActiveKantorByNameKeyword($name);

        return $this->formatReturnData($kantors);
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param PosisiHelper $helper
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/kantors/kepala_kantor', methods: ['POST'])]
    public function getKepalaKantor(ManagerRegistry $doctrine, Request $request, PosisiHelper $helper): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        // Make sure the pegawaiId parameter exists
        if (!array_key_exists('kantorId', $requestData)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the uuid of kantor Entity inside kantorId parameter.'
            ], 404);
        }

        // Make sure the provided data is valid
        $kantorId = $requestData['kantorId'];
        if (empty($kantorId) || is_array($kantorId) || is_bool($kantorId) || !Uuid::isValid($kantorId)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the valid uuid of Kantor Entity.'
            ], 404);
        }

        // Fetch the kantor data
        $kantor = $doctrine
            ->getRepository(Kantor::class)
            ->findOneBy(['id' => $kantorId]);

        // If no data found, return
        if (null === $kantor) {
            return $this->json([
                'code' => 404,
                'errors' => 'There is no Kantor found with the associated id.'
            ], 404);
        }


        return $this->json([
            'kantorId' => $kantorId,
            'kantorName' => $kantor->getNama(),
            'kepala_kantor' => $helper->getKepalaKantorFromKantor($kantor)
        ]);
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
}
