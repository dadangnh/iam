<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use JsonException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class JsonLoginTest extends ApiTestCase
{
    public function testGetAplikasi(): void
    {
        // Expect to get 401 status code because no Token supplied
        try {
            $response = static::createClient()->request('GET', '/api/aplikasis');
        } catch (TransportExceptionInterface $e) {
        }
        self::assertResponseStatusCodeSame(401);
    }

    public function testAuthentication(): void
    {
        $defaultCredential = 'admin';
        try {
            $response = static::createClient()->request('POST', '/api/authentication', [
                'headers' => [
                    'content-type' => 'application/json',
                    'accept' => 'application/json',
                ],
                'body' => json_encode([
                    'username' => $defaultCredential,
                    'password' => $defaultCredential,
                ], JSON_THROW_ON_ERROR)
            ]);
        } catch (TransportExceptionInterface | JsonException $e) {
        }

        self::assertResponseStatusCodeSame(200);
        self::assertResponseIsSuccessful();
    }
}
