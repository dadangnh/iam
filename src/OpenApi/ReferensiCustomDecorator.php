<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;
use ArrayObject;

class ReferensiCustomDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['PostReferensiLegacyDataRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'eselon_ids' => [
                    'type' => 'array',
                    'example' => ["uuid_1", "uuid_2"],
                ],
                'jabatan_ids' => [
                    'type' => 'array',
                    'example' => ["uuid_1", "uuid_2"],
                ],
                'jenis_kantor_ids' => [
                    'type' => 'array',
                    'example' => ["uuid_1", "uuid_2"],
                ],
                'kantor_ids' => [
                    'type' => 'array',
                    'example' => ["uuid_1", "uuid_2"],
                ],
                'unit_ids' => [
                    'type' => 'array',
                    'example' => ["uuid_1", "uuid_2"],
                ],
            ],
        ]);

        $schemas['PostReferensiLegacyDataResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'eselon_ids' => [
                    'type' => 'array',
                    'example' => [],
                    'readOnly' => true,
                ],
                'jabatan_ids' => [
                    'type' => 'array',
                    'example' => [],
                    'readOnly' => true,
                ],
                'jenis_kantor_ids' => [
                    'type' => 'array',
                    'example' => [],
                    'readOnly' => true,
                ],
                'kantor_ids' => [
                    'type' => 'array',
                    'example' => [],
                    'readOnly' => true,
                ],
                'unit_ids' => [
                    'type' => 'array',
                    'example' => [],
                    'readOnly' => true,
                ],
            ],
        ]);


        $legacyDataReferenceItem = new Model\PathItem(
            ref: 'Referensi',
            post: new Model\Operation(
                operationId: 'postLegacyDataItems',
                tags: ['Referensi'],
                responses: [
                    '200' => [
                        'description' => 'Successful response',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/PostReferensiLegacyDataResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get entity data include the legacy data',
                requestBody: new Model\RequestBody(
                    description: 'Get Entity data including legacy data for the specified entities,
                                        For the keyword, use the following (can be used simultaneous),
                                        and provide the value with arrays of uuid
                                        <b>eselon_ids / jabatan_ids / jenis_kantor_ids / kantor_ids / unit_ids </b>',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/PostReferensiLegacyDataRequest',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $openApi->getPaths()->addPath('/api/referensi/legacy_data', $legacyDataReferenceItem);

        return $openApi;
    }
}
