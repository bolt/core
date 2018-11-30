<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * User functionality Twig extension.
 */
class UserExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getuser', [$this, 'dummy']),
            new TwigFunction('getuserid', [$this, 'dummy']),
        ];
    }

    public function dummy($input = null)
    {
        return $input;
    }
}
