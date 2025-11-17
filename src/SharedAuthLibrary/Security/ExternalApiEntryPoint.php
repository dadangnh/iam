<?php

namespace App\SharedAuthLibrary\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class ExternalApiEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $authException = null): \Symfony\Component\HttpFoundation\Response
    {
        return new JsonResponse([
            'status'    => 'fail',
            'code'      => 'FAILED_AUTHENTICATION',
            'message'   => 'Unauthorized API access.',
        ], 401);
    }
}
