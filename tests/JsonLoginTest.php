<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class JsonLoginTest extends ApiTestCase
{
    public function testGetAgamas()
    {
        // Expect to get 401 status code because no Token supplied
        $response = static::createClient()->request('GET', '/api/agamas');
        $this->assertResponseStatusCodeSame(401);
    }
}
