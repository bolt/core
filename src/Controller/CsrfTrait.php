<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

trait CsrfTrait
{
    /**
     * @var CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    protected function validateCsrf(Request $request, string $tokenId): void
    {
        $token = new CsrfToken($tokenId, $request->request->get('_csrf_token'));

        if (! $this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
    }
}
