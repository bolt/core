<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Configuration\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Tightenco\Collect\Support\Collection;

class Canonical
{
    /** @var Config */
    private $config;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Request */
    private $request;

    /** @var string */
    private $scheme = null;

    /** @var string */
    private $host = null;

    /** @var int */
    private $port = null;

    /** @var string */
    private $path = null;

    /** @var RouterInterface */
    private $router;

    /** @var string */
    private $defaultLocale;

    public function __construct(Config $config, UrlGeneratorInterface $urlGenerator, RequestStack $requestStack, RouterInterface $router, string $defaultLocale)
    {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->defaultLocale = $defaultLocale;

        $this->init();
    }

    public function init(): void
    {
        // Ensure in request cycle (even for override).
        if ($this->request === null) {
            return;
        }

        $requestUrl = parse_url($this->request->getSchemeAndHttpHost());

        $configCanonical = (string) $this->config->get('general/canonical', $this->request->getSchemeAndHttpHost());

        if (mb_strpos($configCanonical, 'http') !== 0) {
            $configCanonical = $requestUrl['scheme'] . '://' . $configCanonical;
        }

        $configUrl = parse_url($configCanonical);

        $this->setScheme($configUrl['scheme']);
        $this->setHost($configUrl['host']);
        $this->setPort($configUrl['port'] ?? null);

        $_SERVER['CANONICAL_HOST'] = $configUrl['host'];
        $_SERVER['CANONICAL_SCHEME'] = $configUrl['scheme'];
    }

    public function get(?string $route = null, array $params = []): ?string
    {
        // Ensure request has been matched
        if (! $this->request->attributes->get('_route')) {
            return null;
        }

        if ($route) {
            $this->setPath($route, $params);
        }

        return sprintf(
            '%s://%s%s%s',
            $this->getScheme(),
            $this->getHost(),
            ($this->getPort() ? ':' . $this->getPort() : ''),
            $this->getPath()
        );
    }

    /**
     * Override the initial UrlGeneratorInterface
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Override the scheme
     */
    public function setScheme(string $scheme): void
    {
        $this->scheme = trim($scheme, ':/');
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(?int $port): void
    {
        $this->port = $port;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getPath(): string
    {
        if ($this->path === null) {
            $route = $this->request->attributes->get('_route');
            $params = $this->request->attributes->get('_route_params');
            $canonicalRoute = $this->getCanonicalRoute($route, $params);

            $this->path = $this->urlGenerator->generate(
                $canonicalRoute,
                $params,
                UrlGeneratorInterface::ABSOLUTE_PATH
            );
        }

        return $this->path;
    }

    public function setPath(?string $route = null, array $params = []): void
    {
        if (! $this->request->attributes->get('_route')) {
            return;
        } elseif (! $route) {
            $route = $this->request->attributes->get('_route');
        }

        $canonicalRoute = $this->getCanonicalRoute($route, $params);

        try {
            $this->path = $this->urlGenerator->generate(
                $canonicalRoute,
                $params
            );
        } catch (InvalidParameterException | MissingMandatoryParametersException $e) {
            // Just use the current URL /shrug
            $this->request->getUri();
        }
    }

    public function getCanonicalRoute(string $route, array &$params = []): string
    {
        $routes = new Collection($this->router->getRouteCollection()->getIterator());
        $currentController = $routes->get($route)->getDefault('_controller');

        $routes = collect($routes->filter(function (Route $route) use ($currentController) {
            return $route->getDefault('_controller') === $currentController;
        })->keys());

        // If only one route matched, return that.
        if ($routes->count() === 1) {
            return $routes->first();
        }

        // If no locale or locale is not default, get the first route which is named *_locale
        if (array_key_exists('_locale', $params) && $params['_locale'] !== $this->defaultLocale) {
            return $routes->filter(function (string $name) {
                return fnmatch('*locale', $name);
            })->first();
        }

        // Unset _locale so that it is not passed as query param to url.
        unset($params['_locale']);

        // Otherwise, get the first route that is not *_locale
        return $routes->filter(function (string $name) {
            return ! fnmatch('*locale', $name);
        })->first();
    }
}
