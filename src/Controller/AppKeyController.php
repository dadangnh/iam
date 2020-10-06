<?php


namespace App\Controller;

use App\Api\Resource\AppKey;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AppKeyController
{
    public function __construct()
    {
    }

    /**
     * @Route(
     *     name="appkey",
     *     path="/app_key",
     *     methods={"POST"},
     *     defaults={
     *          "_api_resource_class"=AppKey::class,
     *     }
     * )
     * @param AppKey $appKey
     * @return JsonResponse
     */
    public function __invoke(AppKey $appKey):JsonResponse
    {
        // TODO: Implement __invoke() method.
        return new JsonResponse('task success json', 201);
    }
}