<?php

declare(strict_types=1);

namespace Bolt\Controller\Async;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Async controller for embed routes.
 */
final class Embed
{
    /**
     * @Route("/embed", name="bolt_embed")
     */
    public function embed(Request $request): JsonResponse
    {
        try {
            $url = $request->query->get('url');
            $info = \Embed\Embed::create($url);

            $providers = $info->getProviders();
            $oembed = $providers['oembed'];

            return new JsonResponse(
                $oembed->getBag()->getAll()
            );
        } catch (\Embed\Exceptions\InvalidUrlException $e) {
            $response = [
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
            return new JsonResponse($response, Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
