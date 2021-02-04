<?php

declare(strict_types=1);

namespace App\SharedAuthLibrary\Security;

class JwtPayloadContainer
{
    private array $payload = [];

    public function setPayload(array $payload): void
    {
        if (empty($this->payload)) {
            $this->payload = $payload;
        }
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
