<?php

namespace App\Controller;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RefreshTokenController extends AbstractController
{
    #[Route('/api/token/expired/clear', name: 'api_token_expired_clear', methods: ['GET'])]
    public function clearExpiredToken(RefreshTokenManagerInterface $refreshTokenManager): Response
    {
        $revokedTokens = $refreshTokenManager->revokeAllInvalid();

        if (0 === count($revokedTokens)) {
            return $this->json([
                'message' => 'ok',
                'revoked_token_counts' => 0,
            ]);
        }

        return $this->json([
            'message' => 'ok',
            'revoked_token_counts' => count($revokedTokens),
        ]);
    }
}
