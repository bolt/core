<?php declare(strict_types=1);

namespace Bolt\Twig\Extension;

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
            // @codingStandardsIgnoreStart
            new TwigFunction('getuser',   [Runtime\UserRuntime::class, 'dummy']),
            new TwigFunction('getuserid', [Runtime\UserRuntime::class, 'dummy']),
            // @codingStandardsIgnoreEnd
        ];
    }
}
