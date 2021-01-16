<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use JsonException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class JsonLoginTest extends ApiTestCase
{
    public function testGetAgamas(): void
    {
        // Expect to get 401 status code because no Token supplied
        try {
            $response = static::createClient()->request('GET', '/api/agamas');
        } catch (TransportExceptionInterface $e) {
        }
        self::assertResponseStatusCodeSame(401);
    }

    public function testPostJsonLogin(): void
    {
        $defaultCredential = 'admin';
        try {
            $response = static::createClient()->request('POST', '/json_login', [
                'headers' => [
                    'content-type' => 'application/json',
                ],
                'body' => json_encode([
                    'username' => $defaultCredential,
                    'password' => $defaultCredential,
                ], JSON_THROW_ON_ERROR)
            ]);
        } catch (TransportExceptionInterface | JsonException $e) {
        }
        self::assertResponseStatusCodeSame(200);
        try {
            self::assertJsonContains(['username' => $defaultCredential]);
        } catch (ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
        }
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
