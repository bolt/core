<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserExtension extends AbstractExtension
{
    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isallowed', [$this, 'isAllowed']),
        ];
    }

    /**
     * @todo Replace with better method, once we've implemented https://github.com/bolt/core/issues/186
     */
    public function isAllowed(): bool
    {
        if ($this->security->getUser()) {
            return true;
        }

        return false;
    }
}
