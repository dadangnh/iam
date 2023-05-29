<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;

class RefreshTokenDecorator implements OpenApiFactoryInterface
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


        $schemas['InvalidateExpiredTokenResponse'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'message' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'revoked_token_counts' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
            ],
        ]);


        $clearExpiredTokenItem = new PathItem(
            ref: 'Clear Expired Refresh Token',
            get: new Operation(
                operationId: 'clearExpiredRefreshToken',
                tags: ['Token'],
                responses: [
                    '200' => [
                        'description' => 'Clear all expired refresh tokens from database.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/InvalidateExpiredTokenResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Clear all expired refresh tokens from database.'
            ),
        );

        $openApi->getPaths()->addPath('/api/token/expired/clear', $clearExpiredTokenItem);

        return $openApi;
    }
}
