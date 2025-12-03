<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use JsonException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Test class with simple access to resources.
 */
class JsonLoginTest extends ApiTestCase
{
    /**
     * Mock the response of the aplikasis endpoint.
     * @return void
     */
    public function testGetAplikasi(): void
    {
        // Expect to get 401 status code because no Token supplied
        try {
            $response = static::createClient()->request('GET', '/api/aplikasis');
        } catch (TransportExceptionInterface $e) {
        }
        self::assertResponseStatusCodeSame(401);
    }

    /**
     * Mock the response of the login endpoint.
     * @return void
     */
    public function testAuthentication(): void
    {
        $defaultCredential = 'admin';
        $defaultCredentialpassword = 'Pajak123';
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
