<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

trait CsrfTrait
{
    /** @var CsrfTokenManagerInterface */
    protected $csrfTokenManager;

    /**
     * @required
     */
    public function setCsrfTokenManager(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    protected function validateCsrf(string $tokenId): void
    {
        $token = new CsrfToken($tokenId, $this->request->get('_csrf_token', $this->request->get('token')));

        if (! $this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
    }
}
