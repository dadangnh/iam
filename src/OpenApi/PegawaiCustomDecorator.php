<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;

class PegawaiCustomDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated
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

        $bulkPegawaiDataFromIdsItem = new PathItem(
            ref: 'Pegawai',
            post: new Operation(
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
                requestBody: new RequestBody(
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

        $fetchAtasanFromPegawaiIdItem = new PathItem(
            ref: 'Pegawai',
            post: new Operation(
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
                requestBody: new RequestBody(
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
