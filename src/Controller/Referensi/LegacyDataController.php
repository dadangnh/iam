<?php

namespace App\Controller\Referensi;

use App\Entity\Organisasi\Eselon;
use App\Entity\Organisasi\Jabatan;
use App\Entity\Organisasi\JenisKantor;
use App\Entity\Organisasi\Kantor;
use App\Entity\Organisasi\Unit;
use Doctrine\Persistence\ManagerRegistry;
use JsonException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

/**
 * Restrict access to this controller only for user
 * @Security("is_granted('ROLE_USER')")
 */
class LegacyDataController extends AbstractController
{
    private const VALID_KEYS = [
        'eselon_ids',
        'jabatan_ids',
        'jenis_kantor_ids',
        'kantor_ids',
        'unit_ids'
    ];

    private const PERSISTENCE_OBJECT_NAME = [
        'eselon_ids' => Eselon::class,
        'jabatan_ids' => Jabatan::class,
        'jenis_kantor_ids' => JenisKantor::class,
        'kantor_ids' => Kantor::class,
        'unit_ids' => Unit::class
    ];

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @throws JsonException
     */
    #[Route('/api/referensi/legacy_data', methods: ['POST'])]
    public function showLegacyData(Request $request): JsonResponse
    {
        // Get the request data
        $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        // Get the result data
        $results = [
            $this->getResultFromRequest('eselon_ids', $requestData),
            $this->getResultFromRequest('jabatan_ids', $requestData),
            $this->getResultFromRequest('jenis_kantor_ids', $requestData),
            $this->getResultFromRequest('kantor_ids', $requestData),
            $this->getResultFromRequest('unit_ids', $requestData)
        ];

        // Reformat, remove null value
        $output = [];
        foreach ($results as $result) {
            if (is_array($result)) {
                $output[] = $result;
            }
        }
        $output = array_merge(...$output);

        // Return
        return $this->json($output);
    }

    /**
     * @param $key
     * @param $requestData
     * @return array|null
     */
    private function getResultFromRequest($key, $requestData): ?array
    {
        $results = [];

        // Check whether keyword is exist in body and valid
        if (array_key_exists($key, $requestData) && in_array($key, self::VALID_KEYS, true)) {
            $requestIds = $requestData[$key];
            if (!is_array($requestIds) || empty($requestIds)) {
                $results[$key] = [
                    'code' => 404,
                    'errors' => 'Please provide the uuid in an array format.'
                ];
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

            if (!empty($invalidUuid)) {
                $results[$key] = [
                    'code' => 404,
                    'errors' => 'Please provide the uuid in an array format. The following id is invalid.',
                    "invalid_uuid" => $invalidUuid
                ];
            }

            // Process the query to repository
            if (empty($results[$key]['errors']) && empty($invalidUuid)) {
                $resultData = $this->doctrine
                    ->getRepository(self::PERSISTENCE_OBJECT_NAME[$key])
                    ->findLegacyDataFromArrayOfIds($listEntityUuid);

                if (empty($resultData)) {
                    $results[$key] = [
                        'code' => 404,
                        'result' => 'No entity found with associated uuid.'
                    ];
                } else {
                    $results[$key] = [
                        'code' => 200,
                        'results' => $resultData
                    ];
                }
            }

            // If valid, return the result
            return $results;
        }

        // return null if user didn't provide the keyword on body
        if (!array_key_exists($key, $requestData) && in_array($key, self::VALID_KEYS, true)) {
            return null;
        }

        // return invalid keyword
        return $results[$key] = [
            'code' => 404,
            'errors' => 'The specified keyword is invalid.'
        ];
    }
}
