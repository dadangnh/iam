<?php

namespace App\SharedAuthLibrary\Security;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ExternalJwtAuthenticator extends AbstractAuthenticator
{
    private readonly string $jwksFile;

    public function __construct(string $jwksFile) {
        $this->jwksFile = $jwksFile;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        try {
            $authHeader = $request->headers->get('Authorization');

            if (!str_starts_with($authHeader, 'Bearer ')) {
                throw new AuthenticationException('Invalid Authorization header.');
            }

            $jwt = substr($authHeader, 7);

            // 1. Parse token
            $serializer = new CompactSerializer();
            $jws        = $serializer->unserialize($jwt);

            if (!file_exists($this->jwksFile)) {
                throw new AuthenticationException("JWKS file not found: " . $this->jwksFile);
            }

            // 2. Ambil JWKS dari external provider
            $jwksData   = json_decode(file_get_contents($this->jwksFile), true);
            $jwkSet     = JWKSet::createFromKeyData($jwksData);

            // 3. Setup verifier dengan AlgorithmManager
            $algorithmManager = new AlgorithmManager([new RS256()]);
            $verifier         = new JWSVerifier($algorithmManager);

            // 4. Verifikasi signature
            $isValid = false;
            foreach ($jwkSet->all() as $jwk) {
                if ($verifier->verifyWithKey($jws, $jwk, 0)) {
                    $isValid = true;
                    break;
                }
            }

            if (!$isValid) {
                throw new AuthenticationException('Invalid JWT Signature.');
            }

            // 4. Ambil payload
            $payload = json_decode($jws->getPayload(), true);

            if (!isset($payload['identifier'])) {
                throw new AuthenticationException('Token missing required identifier.');
            }

            return new SelfValidatingPassport(new UserBadge($payload['identifier']));
        } catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
            // Error saat request JWKS
            throw new AuthenticationException('Unable to fetch JWKS: ' . $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            // Error parsing JWT / JWKS
            throw new AuthenticationException('Invalid token or JWKS data: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Catch semua error lainnya
            throw new AuthenticationException('Authentication failed: ' . $e->getMessage());
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?\Symfony\Component\HttpFoundation\Response
    {
        return null; // lanjut ke controller / API platform
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?\Symfony\Component\HttpFoundation\Response
    {
        return new JsonResponse([
            'status' => 'fail',
            'code'   => 'FAILED_AUTHENTICATION',
            'message'=> $exception->getMessage()
        ], 401);
    }
}
