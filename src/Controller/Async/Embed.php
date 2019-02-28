<?php

declare(strict_types=1);

namespace Bolt\Controller\Async;

use Embed\Embed as EmbedFactory;
use Embed\Exceptions\InvalidUrlException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Security("has_role('ROLE_ADMIN')")
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
    public function __invoke(Request $request): JsonResponse
    {
        $csrfToken = $request->request->get('_csrf_token');
        $token = new CsrfToken('editrecord', $csrfToken);

        if (! $this->csrfTokenManager->isTokenValid($token)) {
            return new JsonResponse(['error' => ['message' => 'Invalid CSRF token']], Response::HTTP_FORBIDDEN);
        }

        try {
            $url = $request->request->get('url');
            $info = EmbedFactory::create($url);
            $oembed = $info->getProviders()['oembed'];

            return new JsonResponse(
                $oembed->getBag()->getAll()
            );
        } catch (InvalidUrlException $e) {
            return new JsonResponse(['error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
    }
}
