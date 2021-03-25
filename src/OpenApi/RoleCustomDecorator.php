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

        $schemas['GetAplikasiByRoleNameRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'role_name' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
            ],
        ]);

        $schemas['GetAplikasiByRoleNameResponse'] = new ArrayObject([
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

        $schemas['GetPermissionsByTokenResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'unique_permissions_count' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'unique_permissions' => [
                    'type' => 'object',
                    'readOnly' => true,
                ],
                'list_per_role' => [
                    'type' => 'object',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['GetPermissionsByRoleNameResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'permissions_count' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'permissions' => [
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

        $aplikasiByRoleName = new Model\PathItem(
            ref: 'Aplikasi',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authorization - Get List of Aplikasi By Role Name'],
                responses: [
                    '200' => [
                        'description' => 'Get Aplikasi',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetAplikasiByRoleNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get Roles from Jabatan Pegawai.',
                requestBody: new Model\RequestBody(
                    description: 'Role Name',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/GetAplikasiByRoleNameRequest',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $permissionsByTokenItem = new Model\PathItem(
            ref: 'Permission',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authorization - Get List of Permissions From Token'],
                responses: [
                    '200' => [
                        'description' => 'Get Permissions',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetPermissionsByTokenResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get Permissions from Token.',
            ),
        );

        $permissionsByRoleNameItem = new Model\PathItem(
            ref: 'Permission',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authorization - Get List of Permissions From Role Name'],
                responses: [
                    '200' => [
                        'description' => 'Get Permissions',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetPermissionsByRoleNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get Permissions from Role Name.',
                requestBody: new Model\RequestBody(
                    description: 'Role Name',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/GetAplikasiByRoleNameRequest',
                            ],
                        ],
                    ]),
                ),

            ),
        );

        $openApi->getPaths()->addPath('/api/get_roles_by_jabatan_pegawai', $roleByJabatanPegawaiItem);
        $openApi->getPaths()->addPath('/api/get_aplikasi_by_token', $aplikasiByTokenItem);
        $openApi->getPaths()->addPath('/api/get_aplikasi_by_role_name', $aplikasiByRoleName);
        $openApi->getPaths()->addPath('/api/get_permissions_by_token', $permissionsByTokenItem);
        $openApi->getPaths()->addPath('/api/get_permissions_by_role_name', $permissionsByRoleNameItem);

        return $openApi;
    }
}
