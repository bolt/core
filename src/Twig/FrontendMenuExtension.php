<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Menu\FrontendMenuBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FrontendMenuExtension extends AbstractExtension
{
    /** @var FrontendMenuBuilderInterface */
    private $menuBuilder;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(FrontendMenuBuilderInterface $menuBuilder, RequestStack $requestStack)
    {
        $this->menuBuilder = $menuBuilder;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];
        $env = ['needs_environment' => true];

        return [
            new TwigFunction('menu', [$this, 'renderMenu'], $env + $safe),
            new TwigFunction('menu_array', [$this, 'getMenu'], $env + $safe),
        ];
    }

    public function getMenu(Environment $twig, ?string $name = null): array
    {
        return $this->menuBuilder->buildMenu($twig, $name);
    }

    public function renderMenu(Environment $twig, ?string $name = null, string $template = 'helpers/_menu.html.twig', string $class = '', bool $withsubmenus = true): string
    {
        $context = [
            'menu' => $this->menuBuilder->buildMenu($twig, $name),
            'class' => $class,
            'withsubmenus' => $withsubmenus,
        ];

        return $twig->render($template, $context);
    }

    public function isCurrent($item): bool
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $currentLocale = $currentRequest->getLocale();
        $uri = $item['uri'] ?? '';
        $currentUrl = str_replace('/' . $currentLocale . '/', '/', $currentRequest->getPathInfo());

        return $uri === $currentUrl;
    }
}
