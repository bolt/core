<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Controller\CsrfTrait;
use Embed\Embed as EmbedFactory;
use Embed\Exceptions\InvalidUrlException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Security("is_granted('fetch_embed_data')")
 */
class EmbedController implements AsyncZoneInterface
{
    use CsrfTrait;

    /** @var Request */
    private $request;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, RequestStack $requestStack)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @Route("/embed", name="bolt_async_embed", methods={"POST"})
     */
    public function fetchEmbed(): JsonResponse
    {
        try {
            $this->validateCsrf('editrecord');
        } catch (InvalidCsrfTokenException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Invalid CSRF token',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $url = $this->request->request->get('url');
            $info = EmbedFactory::create($url);
            $oembed = $info->getProviders()['oembed'];

            $response = $oembed->getBag()->getAll();

            if ($oembed->getProviderName() === 'YouTube') {
                $html = $oembed->getCode();

                if (! preg_match('/title=([^\s]+)/', $html)) {
                    $response['html'] = preg_replace('/>/', sprintf(' title="%s">', $oembed->getTitle()), $html, 1);
                }
            }

            return new JsonResponse($response);
        } catch (InvalidUrlException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
