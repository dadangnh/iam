<?php
declare(strict_types=1);

namespace App\Swagger;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerDecorator implements NormalizerInterface
{

    private $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        $docs['components']['schemas']['Token'] = [
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $docs['components']['schemas']['Credentials'] = [
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                ],
                'password' => [
                    'type' => 'string',
                ],
            ],
        ];

        $docs['components']['schemas']['putRefreshToken'] = [
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                ],
            ],
        ];

        $docs['components']['schemas']['RefreshToken'] = [
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $docs['components']['schemas']['ChangeUserPassword'] = [
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                ],
                'old_password' => [
                    'type' => 'string',
                ],
                'new_password' => [
                    'type' => 'string',
                ],
            ],
        ];

        $docs['components']['schemas']['UserPasswordChangeResponse'] = [
            'type' => 'object',
            'properties' => [
                'status' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $docs['components']['schemas']['sendResponse'] = [
            'type' => 'object',
            'properties' => [
                'status' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $docs['components']['schemas']['GetRoleByJabatanRequest'] = [
            'type' => 'object',
            'properties' => [
                'id_jabatan' => [
                    'type' => 'string',
                ],
            ],
        ];

        $docs['components']['schemas']['GetRoleByJabatanResponse'] = [
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
        ];

        $tokenDocumentation = [
            'paths' => [
                '/api/authentication' => [
                    'post' => [
                        'tags' => ['Token'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => 'Get JWT token to login.',
                        'requestBody' => [
                            'description' => 'Create new JWT Token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Credentials',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get JWT token',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Token',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/token/refresh' => [
                    'post' => [
                        'tags' => ['Refresh Token'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => 'Get JWT token to login with Refresh Token.',
                        'requestBody' => [
                            'description' => 'Create new JWT Token from Refresh Token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/putRefreshToken',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get New JWT token',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/RefreshToken',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/json_login' => [
                    'post' => [
                        'tags' => ['JSON Login'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => 'Get User data.',
                        'requestBody' => [
                            'description' => 'Get User data',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Credentials',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get User data',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/sendResponse',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/whoami' => [
                    'post' => [
                        'tags' => ['Who Am I'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => 'Get User data from valid token.',
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get User data from valid token',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/sendResponse',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/change_user_password' => [
                    'post' => [
                        'tags' => ['Change User Password'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => 'Change User Password',
                        'requestBody' => [
                            'description' => 'Post Username, Old Password, and New Password to change user password',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ChangeUserPassword',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Json Status',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/UserPasswordChangeResponse',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/get_roles_by_jabatan' => [
                    'post' => [
                        'tags' => ['Get list of Roles by Jabatan Pegawai'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => 'Get list of Roles by Jabatan Pegawai',
                        'requestBody' => [
                            'description' => 'Post the Jabatan Pegawai id',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/GetRoleByJabatanRequest',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'List of Roles',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/GetRoleByJabatanResponse',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return array_merge_recursive($docs, $tokenDocumentation);
    }
}
