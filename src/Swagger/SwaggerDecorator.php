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
            ],
        ];

        return array_merge_recursive($docs, $tokenDocumentation);
    }
}
