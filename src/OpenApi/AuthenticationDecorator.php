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

        $schemas['RefreshTokenRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'your_refresh_token_here',
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


        $schemas['CheckUserIdentifierRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'the_user_name',
                ],
            ],
        ]);

        $schemas['CheckUserIdentifierValidResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'message' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['CheckUserIdentifierInvalidResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'error' => [
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
                operationId: 'postRefreshTokenItem',
                tags: ['Token'],
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

        $checkUserIdentifierItem = new Model\PathItem(
            ref: 'Check user identifier',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['User'],
                responses: [
                    '200' => [
                        'description' => 'Valid user identifier',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/CheckUserIdentifierValidResponse',
                                ],
                            ],
                        ],
                    ],
                    '404' => [
                        'description' => 'Invalid user identifier',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/CheckUserIdentifierInvalidResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Check whether username is valid or not.',
                requestBody: new Model\RequestBody(
                    description: 'Check whether username is valid or not',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/CheckUserIdentifierRequest',
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
                operationId: 'postCredentialsToChangePasswordItem',
                tags: ['User'],
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

        $whoAmIOldItem = new Model\PathItem(
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

        $whoAmIItem = new Model\PathItem(
            ref: 'Token',
            post: new Model\Operation(
                operationId: 'postWhoAmIItem',
                tags: ['Token'],
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
                summary: 'Get User Identifier Data From Token.',
                requestBody: null,
            ),
        );

        $openApi->getPaths()->addPath('/api/authentication', $authItem);
        $openApi->getPaths()->addPath('/api/token/refresh', $refreshTokenItem);
        $openApi->getPaths()->addPath('/json_login', $jsonLoginItem);
        $openApi->getPaths()->addPath('/api/users/change_password', $changePasswordItem);
        $openApi->getPaths()->addPath('/api/whoami', $whoAmIOldItem);
        $openApi->getPaths()->addPath('/api/token/whoami', $whoAmIItem);
        $openApi->getPaths()->addPath('/api/users/check_identifier', $checkUserIdentifierItem);

        return $openApi;
    }
}
