<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Controller\CsrfTrait;
use Embed\Embed as EmbedFactory;
use Psr\Http\Client\RequestExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(attribute: 'fetch_embed_data')]
class EmbedController implements AsyncZoneInterface
{
    use CsrfTrait;

    private ?Request $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    #[Route(path: '/embed', name: 'bolt_async_embed', methods: [Request::METHOD_POST])]
    public function fetchEmbed(Request $request): JsonResponse
    {
        try {
            $this->validateCsrf($request, 'editrecord');
        } catch (InvalidCsrfTokenException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Invalid CSRF token',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $url = $this->request?->request->getString('url') ?? '';
            $info = (new EmbedFactory())->get($url);
            $oembed = $info->getOEmbed();

            $response = $oembed->all();

            if ($oembed->get('provider_name') === 'YouTube') {
                $html = $oembed->get('html');

                if (! preg_match('/title=([^\s]+)/', (string) $html)) {
                    $response['html'] = preg_replace('/>/', sprintf(' title="%s">', $oembed->get('title')), (string) $html, 1);
                }
            }

            return new JsonResponse($response);
        } catch (RequestExceptionInterface $e) {
            return new JsonResponse([
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
