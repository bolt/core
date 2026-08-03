<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Controller\CsrfTrait;
use Bolt\Utils\UrlSafetyChecker;
use Embed\Embed as EmbedFactory;
use Embed\Http\Crawler;
use Symfony\Component\HttpClient\NoPrivateNetworkHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

#[IsGranted(attribute: 'fetch_embed_data')]
class EmbedController implements AsyncZoneInterface
{
    use CsrfTrait;

    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {
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

        $url = $request->request->getString('url') ?? '';

        // Prevent SSRF: only fetch URLs with an allowed scheme that resolve to
        // a public IP address (not a private, reserved or loopback address).
        try {
            UrlSafetyChecker::assertSafe($url);
        } catch (Throwable $e) {
            return new JsonResponse([
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // `UrlSafetyChecker` above only validates the submitted URL. Embed
            // follows redirects (HTTP and `<meta http-equiv="refresh">`) while
            // crawling, so it is given a client that re-validates the address
            // actually connected to on every hop. This prevents SSRF bypasses
            // via redirects or DNS rebinding to an internal address.
            $client = new Psr18Client(new NoPrivateNetworkHttpClient($this->httpClient));
            $info = (new EmbedFactory(new Crawler($client)))->get($url);
            $oembed = $info->getOEmbed();

            $response = $oembed->all();

            if ($oembed->get('provider_name') === 'YouTube') {
                $html = $oembed->get('html');

                if (! preg_match('/title=([^\s]+)/', (string) $html)) {
                    $response['html'] = preg_replace('/>/', sprintf(' title="%s">', $oembed->get('title')), (string) $html, 1);
                }
            }

            return new JsonResponse($response);
        } catch (Throwable $e) {
            return new JsonResponse([
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
