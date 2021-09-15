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

        $schemas['GetMappingByRoleRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'role_name' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
                'key_data' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
            ],
        ]);

        $schemas['GetMappingByRoleResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'role_name' => [
                    'type' => 'string',
                    'readOnly' => true,
                    'example' => 'ROLE_ADMIN',
                ],
                'key_data' => [
                    'type' => 'String',
                    'readOnly' => true,
                    'example' => 'user',
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
                operationId: 'postTokenToGetAplikasis',
                tags: ['Token'],
                responses: [
                    '200' => [
                        'description' => 'List all aplikasis granted for a token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetAplikasiByTokenResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'List all aplikasis granted for a token.',
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
                summary: 'Get list of Aplikasi from Role Name',
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

        $aplikasiByRoleNameNewItem = new Model\PathItem(
            ref: 'Aplikasi',
            get: new Model\Operation(
                operationId: 'getAplikasiByRoleName',
                tags: ['Role'],
                responses: [
                    '200' => [
                        'description' => 'Get List of Aplikasi',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetAplikasiByRoleNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get list of Aplikasi from Role Name',
                parameters: [new Model\Parameter(
                    'name',
                    'path',
                    'Please provide the role name in all capital letter',
                    true
                )]
            ),
        );

        $allAplikasiByRoleNameNewItem = new Model\PathItem(
            ref: 'Aplikasi',
            get: new Model\Operation(
                operationId: 'getAllAplikasiByRoleName',
                tags: ['Role'],
                responses: [
                    '200' => [
                        'description' => 'Get List of All Aplikasi Including the Inactive/On Development One',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetAplikasiByRoleNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get List of All Aplikasi Including the Inactive/On Development from Role Name',
                parameters: [new Model\Parameter(
                    'name',
                    'path',
                    'Please provide the role name in all capital letter',
                    true
                )]
            ),
        );

        $allAplikasiByTokenItem = new Model\PathItem(
            ref: 'Aplikasi',
            post: new Model\Operation(
                operationId: 'postTokenToGetAllAplikasis',
                tags: ['Token'],
                responses: [
                    '200' => [
                        'description' => 'Get Aplikasi data from Token, including unreleased application',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetAplikasiByTokenResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get All Aplikasi from Token Including the Inactive/On Development One.',
            ),
        );

        $allAplikasiByRoleName = new Model\PathItem(
            ref: 'Aplikasi',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authorization - Get List of All Aplikasi By Role Name (including unreleased one)'],
                responses: [
                    '200' => [
                        'description' => 'Get Aplikasi data from role name, including unreleased application',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetAplikasiByRoleNameResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get list of Aplikasi from Role Name.',
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
                operationId: 'postTokenShowPermissions',
                tags: ['Token'],
                responses: [
                    '200' => [
                        'description' => 'Show Permissions from Token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetPermissionsByTokenResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Show Permissions from Token.',
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


        $permissionsByRoleNameGetItem = new Model\PathItem(
            ref: 'Permission',
            get: new Model\Operation(
                operationId: 'getPermissionByRoleName',
                tags: ['Role'],
                responses: [
                    '200' => [
                        'description' => 'Get Permissions From Role Name',
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
                parameters: [new Model\Parameter(
                    'name',
                    'path',
                    'Please provide the role name in all capital letter',
                    true
                )]
            ),
        );

        $mappingByRole = new Model\PathItem(
            ref: 'Jabatans',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Role'],
                responses: [
                    '200' => [
                        'description' => 'Get Data Mapping From Role,
                                        For the key_data field, please select one of the
                                        <b>user/jabatan/unit/kantor/jenis_kantor/eselon/group</b>',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetMappingByRoleResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get list of data from Role Name',
                requestBody: new Model\RequestBody(
                    description: 'Get Mapping Data',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/GetMappingByRoleRequest',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $openApi->getPaths()->addPath('/api/get_roles_by_jabatan_pegawai', $roleByJabatanPegawaiItem);
        $openApi->getPaths()->addPath('/api/token/aplikasis', $aplikasiByTokenItem);
        $openApi->getPaths()->addPath('/api/token/all_aplikasis', $allAplikasiByTokenItem);
        $openApi->getPaths()->addPath('/api/token/permissions', $permissionsByTokenItem);
        $openApi->getPaths()->addPath('/api/roles/mapping', $mappingByRole);
        $openApi->getPaths()->addPath('/api/roles/{name}/aplikasis', $aplikasiByRoleNameNewItem);
        $openApi->getPaths()->addPath('/api/roles/{name}/all_aplikasis', $allAplikasiByRoleNameNewItem);
        $openApi->getPaths()->addPath('/api/roles/{name}/permissions', $permissionsByRoleNameGetItem);

        return $openApi;
    }
}
