<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;
use ArrayObject;
class RoleCustomDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['GetRoleByJabatanPegawaiRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'id_jabatan_pegawai' => [
                    'type' => 'string',
                    'example' => 'uuid',
                ],
            ],
        ]);

        $schemas['GetRoleByJabatanPegawaiResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'roles_count' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'roles' => [
                    'type' => 'object',
                    'readOnly' => true,
                ],
            ],
        ]);


        $schemas['GetAplikasiByTokenResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'aplikasi_count' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'aplikasi' => [
                    'type' => 'object',
                    'readOnly' => true,
                ],
            ],
        ]);

        $roleByJabatanPegawaiItem = new Model\PathItem(
            ref: 'Roles',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authorization - Get Roles From Jabatan Pegawai'],
                responses: [
                    '200' => [
                        'description' => 'Get Roles',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetRoleByJabatanPegawaiResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get Roles from Jabatan Pegawai.',
                requestBody: new Model\RequestBody(
                    description: 'List of Roles',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/GetRoleByJabatanPegawaiRequest',
                            ],
                        ],
                    ]),
                ),
            ),
        );


        $aplikasiByTokenItem = new Model\PathItem(
            ref: 'Aplikasi',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authorization - Get List of Aplikasi From Token'],
                responses: [
                    '200' => [
                        'description' => 'Get Aplikasi',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetAplikasiByTokenResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get Aplikasi from Token.',
            ),
        );

        $openApi->getPaths()->addPath('/api/get_roles_by_jabatan_pegawai', $roleByJabatanPegawaiItem);
        $openApi->getPaths()->addPath('/api/get_aplikasi_by_token', $aplikasiByTokenItem);

        return $openApi;

    }
}
