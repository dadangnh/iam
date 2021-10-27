<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;
use ArrayObject;

class OrganisasiCustomDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['GetActiveKantorByKantorNameRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'kantor_name' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
            ],
        ]);

        $schemas['GetActiveKantorByKantorNameResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'kantor_count' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'kantors' => [
                    'type' => 'object',
                    'readOnly' => true,
                ],
            ],
        ]);

        $activeKantorByKantorNameItem = new Model\PathItem(
            ref: 'Kantor',
            get: new Model\Operation(
                operationId: 'getActiveKantorByKantorName',
                tags: ['Kantor'],
                responses: [
                    '200' => [
                        'description' => 'Get List of Active Kantor',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetActiveKantorByKantorNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get list of Active Kantor from Kantor Name',
                parameters: [new Model\Parameter(
                    'name',
                    'path',
                    'Please provide the kantor name. Minimum consist of 3 character, case insensitive.',
                    true
                )]
            ),
        );

        $allActiveKantorItem = new Model\PathItem(
            ref: 'Kantor',
            get: new Model\Operation(
                operationId: 'getAllActiveKantor',
                tags: ['Kantor'],
                responses: [
                    '200' => [
                        'description' => 'Get List of All Active Kantor',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetActiveKantorByKantorNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get list of All Active Kantor',
            ),
        );

        $schemas['GetActiveUnitByUnitNameRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'unit_name' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
            ],
        ]);

        $schemas['GetActiveUnitByUnitNameResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'unit_count' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'units' => [
                    'type' => 'object',
                    'readOnly' => true,
                ],
            ],
        ]);

        $activeUnitByUnitNameItem = new Model\PathItem(
            ref: 'Unit',
            get: new Model\Operation(
                operationId: 'getActiveUnitByUnitName',
                tags: ['Unit'],
                responses: [
                    '200' => [
                        'description' => 'Get List of Active Unit',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetActiveUnitByUnitNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get list of Active Unit from Unit Name',
                parameters: [new Model\Parameter(
                    'name',
                    'path',
                    'Please provide the unit name. Minimum consist of 3 character, case insensitive.',
                    true
                )]
            ),
        );

        $allActiveUnitItem = new Model\PathItem(
            ref: 'Unit',
            get: new Model\Operation(
                operationId: 'getAllActiveUnit',
                tags: ['Unit'],
                responses: [
                    '200' => [
                        'description' => 'Get List of All Active Unit',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetActiveUnitByUnitNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get list of All Active Unit',
            ),
        );

        $schemas['GetActiveJenisKantorByJenisKantorNameRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'jenis_kantor_name' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
            ],
        ]);

        $schemas['GetActiveJenisKantorByJenisKantorNameResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'jenis_kantor_count' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'jenis_kantors' => [
                    'type' => 'object',
                    'readOnly' => true,
                ],
            ],
        ]);

        $activeJenisKantorByJenisKantorNameItem = new Model\PathItem(
            ref: 'JenisKantor',
            get: new Model\Operation(
                operationId: 'getActiveJenisKantorByJenisKantorName',
                tags: ['JenisKantor'],
                responses: [
                    '200' => [
                        'description' => 'Get List of Active Jenis Kantor',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetActiveJenisKantorByJenisKantorNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get list of Active Jenis Kantor from Jenis Kantor Name',
                parameters: [new Model\Parameter(
                    'name',
                    'path',
                    'Please provide the jenis kantor name. Minimum consist of 3 character, case insensitive.',
                    true
                )]
            ),
        );

        $allActiveJenisKantorItem = new Model\PathItem(
            ref: 'JenisKantor',
            get: new Model\Operation(
                operationId: 'getAllActiveJenisKantor',
                tags: ['JenisKantor'],
                responses: [
                    '200' => [
                        'description' => 'Get List of All Active Jenis Kantor',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetActiveJenisKantorByJenisKantorNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get list of All Active Jenis Kantor',
            ),
        );

        $schemas['GetActiveJabatanByJabatanNameRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'jabatan_name' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
            ],
        ]);

        $schemas['GetActiveJabatanByJabatanNameResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'jabatan_count' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'jabatans' => [
                    'type' => 'object',
                    'readOnly' => true,
                ],
            ],
        ]);

        $activeJabatanByJabatanNameItem = new Model\PathItem(
            ref: 'Jabatan',
            get: new Model\Operation(
                operationId: 'getActiveJabatanByJabatanName',
                tags: ['Jabatan'],
                responses: [
                    '200' => [
                        'description' => 'Get List of Active Jabatan',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetActiveJabatanByJabatanNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get list of Active Jabatan from Jabatan Name',
                parameters: [new Model\Parameter(
                    'name',
                    'path',
                    'Please provide the jabatan name. Minimum consist of 3 character, case insensitive.',
                    true
                )]
            ),
        );

        $allActiveJabatanItem = new Model\PathItem(
            ref: 'Jabatan',
            get: new Model\Operation(
                operationId: 'getAllActiveJabatan',
                tags: ['Jabatan'],
                responses: [
                    '200' => [
                        'description' => 'Get List of All Active Jabatan',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetActiveJabatanByJabatanNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get list of All Active Jabatan',
            ),
        );

        $schemas['postKepalaKantorFromKantorIdRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'kantorId' => [
                    'type' => 'string',
                    'example' => "uuid",
                ],
            ],
        ]);

        $schemas['postKepalaKantorFromKantorIdResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'kantor' => [
                    'type' => 'array',
                    'example' => [],
                    'readOnly' => true,
                ],
            ],
        ]);

        $fetchKepalaKantorFromKantorIdItem = new Model\PathItem(
            ref: 'Kantor',
            post: new Model\Operation(
                operationId: 'postKepalaKantorFromKantorIdItem',
                tags: ['Kantor'],
                responses: [
                    '200' => [
                        'description' => 'Successful response',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/postKepalaKantorFromKantorIdResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get Kepala Kantor data from Kantor Id',
                requestBody: new Model\RequestBody(
                    description: 'Get Kepala kantor data from kantor uuid',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/postKepalaKantorFromKantorIdRequest',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $openApi->getPaths()->addPath('/api/kantors/active/show_all', $allActiveKantorItem);
        $openApi->getPaths()->addPath('/api/kantors/active/{name}', $activeKantorByKantorNameItem);
        $openApi->getPaths()->addPath('/api/kantors/kepala_kantor', $fetchKepalaKantorFromKantorIdItem);
        $openApi->getPaths()->addPath('/api/units/active/show_all', $allActiveUnitItem);
        $openApi->getPaths()->addPath('/api/units/active/{name}', $activeUnitByUnitNameItem);
        $openApi->getPaths()->addPath('/api/jenis_kantors/active/show_all', $allActiveJenisKantorItem);
        $openApi->getPaths()->addPath('/api/jenis_kantors/active/{name}', $activeJenisKantorByJenisKantorNameItem);
        $openApi->getPaths()->addPath('/api/jabatans/active/show_all', $allActiveJabatanItem);
        $openApi->getPaths()->addPath('/api/jabatans/active/{name}', $activeJabatanByJabatanNameItem);

        return $openApi;
    }
}
