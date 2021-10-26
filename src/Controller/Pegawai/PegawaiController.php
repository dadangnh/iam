<?php

namespace App\Controller\Pegawai;

use App\Entity\Pegawai\Pegawai;
use JetBrains\PhpStorm\Pure;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class PegawaiController extends AbstractController
{
    /**
     * @throws JsonException
     */
    #[Route('/api/pegawais/mass_fetch', methods: ['POST'])]
    public function getPegawaiDataFromArrayOfUuid(Request $request): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $requestIds = $requestData['pegawaiIds'];

        // Check whether the request ids is not null
        if (!is_array($requestIds) || empty($requestIds)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the uuid in an array format.'
            ], 404);
        }

        // Make sure only accept Uuid
        $invalidUuid = $listEntityUuid = [];
        foreach ($requestIds as $id) {
            if (!Uuid::isValid($id)) {
                $invalidUuid[] = $id;
            } else {
                $listEntityUuid[] = $id;
            }
        }

        // If contains invalid uuid, return
        if (!empty($invalidUuid)) {
            return $this->json([
                'code' => 404,
                'errors' => 'Please provide the uuid in an array format.',
                'invalidUuids' => $invalidUuid
            ], 404);
        }

        // Get all the data
        $pegawais = $this->getDoctrine()
            ->getRepository(Pegawai::class)
            ->getPegawaisFromArrayOfUuid($listEntityUuid);

        // If there is no data, return
        if (empty($pegawais)) {
            return $this->json([
                'code' => 200,
                'messages' => 'No Pegawais with provided ids found.'
            ]);
        }

        // Make ordered result as requested
        $formattedOutput = $this->orderedOutput($requestIds, $pegawais);
        return $this->json($formattedOutput);
    }

    /**
     * @param $requestIds
     * @param $pegawais
     * @return array
     */
    #[Pure] private function orderedOutput($requestIds, $pegawais): array
    {
        $result = $orderedResult = [];

        /** @var Pegawai $pegawai */
        foreach ($pegawais as $pegawai) {
            $result[(string) $pegawai->getId()] = $pegawai;
        }

        foreach ($requestIds as $id) {
            if (!array_key_exists($id, $result)) {
                $result[$id] = null;
            }
        }

        foreach ($requestIds as $id) {
            $orderedResult[$id] = $result[$id];
        }

        return $orderedResult;
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
