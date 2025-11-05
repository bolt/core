<?php

declare(strict_types=1);

namespace Bolt\Exception;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class DisabledUserLoginAttemptException extends BadCredentialsException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey(): string
    {
        return 'User is disabled.';
    }
}
