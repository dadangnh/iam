<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;
use ArrayObject;

final class AuthenticationDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['AuthTokenResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['AuthCredentials'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'user',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'pass',
                ],
            ],
        ]);

        $schemas['RefreshTokenResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['JsonLoginResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'data' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['WhoAmIResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'data' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['RefreshTokenRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'your_refresh_token_here',
                ],
            ],
        ]);

        $schemas['ChangeUserPasswordRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'username',
                ],
                'old_password' => [
                    'type' => 'string',
                    'example' => 'old_password',
                ],
                'new_password' => [
                    'type' => 'string',
                    'example' => 'new_password',
                ],
            ],
        ]);

        $schemas['ChangeUserPasswordResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'status' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $authItem = new Model\PathItem(
            ref: 'JWT Token',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authentication - Create JWT Token'],
                responses: [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/AuthTokenResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get JWT token to login.',
                requestBody: new Model\RequestBody(
                    description: 'Generate new JWT Token',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/AuthCredentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $refreshTokenItem = new Model\PathItem(
            ref: 'Refresh JWT Token',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authentication - Refresh Token'],
                responses: [
                    '200' => [
                        'description' => 'Get new JWT token by refresh token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/RefreshTokenResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Create new JWT Token from Refresh Token.',
                requestBody: new Model\RequestBody(
                    description: 'Generate new JWT Token using Refresh Token',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/RefreshTokenRequest',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $jsonLoginItem = new Model\PathItem(
            ref: 'Json Login',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authentication - Json Login (deprecated)'],
                responses: [
                    '200' => [
                        'description' => 'User data directly from login',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/JsonLoginResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Make authentication with json response.',
                requestBody: new Model\RequestBody(
                    description: 'Generate json user data from login',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/AuthCredentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $changePasswordItem = new Model\PathItem(
            ref: 'Change Password',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authentication - Change User Password'],
                responses: [
                    '200' => [
                        'description' => 'Status',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/ChangeUserPasswordResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Change user password.',
                requestBody: new Model\RequestBody(
                    description: 'Generate new JWT Token',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/ChangeUserPasswordRequest',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $whoAmIItem = new Model\PathItem(
            ref: 'Who Am I?',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authentication - Get User Data From Token'],
                responses: [
                    '200' => [
                        'description' => 'Status',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/WhoAmIResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get User Object From Token.',
                requestBody: null,
            ),
        );

        $openApi->getPaths()->addPath('/api/authentication', $authItem);
        $openApi->getPaths()->addPath('/api/token/refresh', $refreshTokenItem);
        $openApi->getPaths()->addPath('/json_login', $jsonLoginItem);
        $openApi->getPaths()->addPath('/api/change_user_password', $changePasswordItem);
        $openApi->getPaths()->addPath('/api/whoami', $whoAmIItem);

        return $openApi;
    }
}
