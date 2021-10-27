<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;
use ArrayObject;

class PegawaiCustomDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['PostBulkPegawaiFromIdsRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'pegawaiIds' => [
                    'type' => 'array',
                    'example' => ["uuid_1", "uuid_2"],
                ],
            ],
        ]);

        $schemas['PostBulkPegawaiFromIdsResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'pegawaiIds' => [
                    'type' => 'array',
                    'example' => [],
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['PostAtasanFromPegawaiUidRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'pegawaiId' => [
                    'type' => 'string',
                    'example' => "uuid_1",
                ],
            ],
        ]);

        $schemas['PostAtasanFromPegawaiUidResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'pegawaiIds' => [
                    'type' => 'array',
                    'example' => [],
                    'readOnly' => true,
                ],
            ],
        ]);

        $bulkPegawaiDataFromIdsItem = new Model\PathItem(
            ref: 'Pegawai',
            post: new Model\Operation(
                operationId: 'postBulkPegawaiIdsItem',
                tags: ['Pegawai'],
                responses: [
                    '200' => [
                        'description' => 'Successful response',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/PostBulkPegawaiFromIdsResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get bulk data of Pegawai Entity',
                requestBody: new Model\RequestBody(
                    description: 'Get Pegawai data from array of pegawai uuid',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/PostBulkPegawaiFromIdsRequest',
                            ],
                        ],
                    ]),
                ),
            ),
        );


        $fetchAtasanFromPegawaiIdItem = new Model\PathItem(
            ref: 'Pegawai',
            post: new Model\Operation(
                operationId: 'postAtasanFromPegawaiIdItem',
                tags: ['Pegawai'],
                responses: [
                    '200' => [
                        'description' => 'Successful response',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/PostAtasanFromPegawaiUidResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get Atasan data of Pegawai Uid',
                requestBody: new Model\RequestBody(
                    description: 'Get Atasan data from pegawai uuid',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/PostAtasanFromPegawaiUidRequest',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $openApi->getPaths()->addPath('/api/pegawais/mass_fetch', $bulkPegawaiDataFromIdsItem);
        $openApi->getPaths()->addPath('/api/pegawais/atasan', $fetchAtasanFromPegawaiIdItem);

        return $openApi;
    }
}
