<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CommonExtension extends AbstractExtension
{
    /** @var ContentExtension */
    private $contentExtension;
    /** @var FrontendMenuExtension */
    private $frontendMenuExtension;

    public function __construct(ContentExtension $contentExtension, FrontendMenuExtension $frontendMenuExtension)
    {
        $this->contentExtension = $contentExtension;
        $this->frontendMenuExtension = $frontendMenuExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        $env = ['needs_environment' => true];

        return [
            new TwigFilter('current', [$this, 'isCurrent'], $env),
        ];
    }

    public function isCurrent(Environment $env, $item): bool
    {
        if ($item instanceof Content) {
            return $this->contentExtension->isCurrent($env, $item);
        } elseif (is_iterable($item) && array_key_exists('uri', $item)) {
            return $this->frontendMenuExtension->isCurrent($item);
        }

        return false;
    }
}
