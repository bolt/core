<?php

declare(strict_types=1);

namespace Bolt\Controller\Async;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Async controller for embed routes.
 */
final class Embed
{
    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route(
     *     "/embed",
     *     name="bolt_embed",
     *     methods={"POST"})
     */
    public function embed(Request $request): JsonResponse
    {
        $csrfToken = $request->request->get('_csrf_token');
        $token = new CsrfToken('editrecord', $csrfToken);

        if (! $this->csrfTokenManager->isTokenValid($token)) {
            return new JsonResponse(['error' => ['message' => 'Invalid CSRF token']], Response::HTTP_FORBIDDEN);
        }

        try {
            $url = $request->request->get('url');
            $info = \Embed\Embed::create($url);

            $providers = $info->getProviders();
            $oembed = $providers['oembed'];

            return new JsonResponse(
                $oembed->getBag()->getAll()
            );
        } catch (\Embed\Exceptions\InvalidUrlException $e) {
            return new JsonResponse(['error' => ['message' => $e->getMessage()]], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
