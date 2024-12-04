<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;

final class AuthenticationDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated
    )
    {
    }

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

        $schemas['InvalidateTokenResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'message' => [
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

        $authItem = new PathItem(
            ref: 'JWT Token',
            post: new Operation(
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
                requestBody: new RequestBody(
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

        $refreshTokenItem = new PathItem(
            ref: 'Refresh JWT Token',
            post: new Operation(
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
                requestBody: new RequestBody(
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

        $checkUserIdentifierItem = new PathItem(
            ref: 'Check user identifier',
            post: new Operation(
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
                requestBody: new RequestBody(
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

        $changePasswordItem = new PathItem(
            ref: 'Change Password',
            post: new Operation(
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
                requestBody: new RequestBody(
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

        $whoAmIOldItem = new PathItem(
            ref: 'Who Am I?',
            post: new Operation(
                operationId: 'postWhoAmIItem',
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
                summary: 'Who Am I? This endpoint provide the user information from a token.',
                requestBody: null,
            ),
        );

        $whoAmIItem = new PathItem(
            ref: 'Token',
            post: new Operation(
                operationId: 'postWhoAmIItem',
                tags: ['Token'],
                responses: [
                    '200' => [
                        'description' => 'Who Am I? This endpoint provide the user information from a token.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/WhoAmIResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Who Am I? This endpoint provide the user information from a token.',
                requestBody: null,
            ),
        );

        $invalidateTokenItem = new PathItem(
            ref: 'Logout/ Invalidate Refresh Token ',
            post: new Operation(
                operationId: 'postInvalidateTokenItem',
                tags: ['Token'],
                responses: [
                    '200' => [
                        'description' => 'Logout endpoint by invalidating refresh token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/InvalidateTokenResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Logout endpoint by invalidating refresh token.',
                requestBody: new RequestBody(
                    description: 'Logout endpoint by invalidating refresh token.',
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

        $openApi->getPaths()->addPath('/api/authentication', $authItem);
        $openApi->getPaths()->addPath('/api/token/refresh', $refreshTokenItem);
        $openApi->getPaths()->addPath('/api/users/change_password', $changePasswordItem);
        $openApi->getPaths()->addPath('/api/whoami', $whoAmIOldItem);
        $openApi->getPaths()->addPath('/api/token/whoami', $whoAmIItem);
        $openApi->getPaths()->addPath('/api/users/check_identifier', $checkUserIdentifierItem);
        $openApi->getPaths()->addPath('/api/token/invalidate', $invalidateTokenItem);

        return $openApi;
    }
}
