<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;

class RoleCustomDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated
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

        $roleByJabatanPegawaiItem = new PathItem(
            ref: 'Roles',
            post: new Operation(
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
                requestBody: new RequestBody(
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

        $aplikasiByTokenItem = new PathItem(
            ref: 'Aplikasi',
            post: new Operation(
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

        $aplikasiByRoleName = new PathItem(
            ref: 'Aplikasi',
            post: new Operation(
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
                requestBody: new RequestBody(
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

        $aplikasiByRoleNameNewItem = new PathItem(
            ref: 'Aplikasi',
            get: new Operation(
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
                parameters: [new Parameter(
                    'name',
                    'path',
                    'Please provide the role name in all capital letter',
                    true
                )]
            ),
        );

        $allAplikasiByRoleNameNewItem = new PathItem(
            ref: 'Aplikasi',
            get: new Operation(
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
                parameters: [new Parameter(
                    'name',
                    'path',
                    'Please provide the role name in all capital letter',
                    true
                )]
            ),
        );

        $allAplikasiByTokenItem = new PathItem(
            ref: 'Aplikasi',
            post: new Operation(
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

        $allAplikasiByRoleName = new PathItem(
            ref: 'Aplikasi',
            post: new Operation(
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
                requestBody: new RequestBody(
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

        $permissionsByTokenItem = new PathItem(
            ref: 'Permission',
            post: new Operation(
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

        $permissionsByRoleNameItem = new PathItem(
            ref: 'Permission',
            post: new Operation(
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
                requestBody: new RequestBody(
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

        $permissionsByRoleNameGetItem = new PathItem(
            ref: 'Permission',
            get: new Operation(
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
                parameters: [new Parameter(
                    'name',
                    'path',
                    'Please provide the role name in all capital letter',
                    true
                )]
            ),
        );

        $mappingByRole = new PathItem(
            ref: 'Jabatans',
            post: new Operation(
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
                requestBody: new RequestBody(
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

        $roleByJabatanPegawaisIdGetItem = new PathItem(
            ref: 'Roles',
            get: new Operation(
                operationId: 'getRoleByJabatanPegawaisId',
                tags: ['JabatanPegawai'],
                responses: [
                    '200' => [
                        'description' => 'Get Roles From Jabatan Pegawais ID',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/GetRoleByJabatanPegawaiResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get Roles from Jabatan Pegawais.',
                parameters: [new Parameter(
                    'id',
                    'path',
                    'Please provide the jabatan pegawais UUID',
                    true
                )]
            ),
        );

        $openApi->getPaths()->addPath('/api/jabatan_pegawais/{id}/roles', $roleByJabatanPegawaisIdGetItem);
        $openApi->getPaths()->addPath('/api/roles/mapping', $mappingByRole);
        $openApi->getPaths()->addPath('/api/roles/{name}/aplikasis', $aplikasiByRoleNameNewItem);
        $openApi->getPaths()->addPath('/api/roles/{name}/all_aplikasis', $allAplikasiByRoleNameNewItem);
        $openApi->getPaths()->addPath('/api/roles/{name}/permissions', $permissionsByRoleNameGetItem);
        $openApi->getPaths()->addPath('/api/token/aplikasis', $aplikasiByTokenItem);
        $openApi->getPaths()->addPath('/api/token/all_aplikasis', $allAplikasiByTokenItem);
        $openApi->getPaths()->addPath('/api/token/permissions', $permissionsByTokenItem);

        return $openApi;
    }
}
