<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait CsrfTrait
{
    protected CsrfTokenManagerInterface $csrfTokenManager;

    #[Required]
    public function setCsrfTokenManager(CsrfTokenManagerInterface $csrfTokenManager): void
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    protected function validateCsrf(Request $request, string $tokenId): void
    {
        $token = new CsrfToken($tokenId, $request->get('_csrf_token', $request->get('token')));

        if (! $this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
    }
}
