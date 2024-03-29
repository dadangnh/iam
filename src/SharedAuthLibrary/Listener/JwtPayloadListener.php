<?php

declare(strict_types=1);

namespace App\SharedAuthLibrary\Listener;

use App\SharedAuthLibrary\Security\JwtPayloadContainer;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;

class JwtPayloadListener
{
    private JwtPayloadContainer $jwtPayloadContainer;

    /**
     * JwtPayloadListener constructor.
     * @param JwtPayloadContainer $jwtPayloadContainer
     */
    public function __construct(JwtPayloadContainer $jwtPayloadContainer)
    {
        $this->jwtPayloadContainer = $jwtPayloadContainer;
    }

    /**
     * @param JWTDecodedEvent $event
     */
    public function onJWTDecoded(JWTDecodedEvent $event): void
    {
        $payload = $event->getPayload();
        $this->jwtPayloadContainer->setPayload($payload);
    }
}
