<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Configuration\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\CompiledRoute;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class Canonical
{
    /** @var Request */
    private $request = null;

    /** @var string */
    private $scheme = null;

    /** @var string */
    private $host = null;

    /** @var int */
    private $port = null;

    /** @var string */
    private $path = null;

    public function __construct(
        private readonly Config $config,
        private UrlGeneratorInterface $urlGenerator,
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
        private readonly string $defaultLocale
    ) {
    }

    public function getRequest(): Request
    {
        if ($this->request === null) {
            // Use default value.
            $this->setRequest();
        }

        return $this->request;
    }

    public function setRequest(?Request $request = null): void
    {
        // Default to current request (if any).
        if ($request === null) {
            $request = $this->requestStack->getCurrentRequest() ?? Request::createFromGlobals();
        }

        $this->request = $request;

        // Nothing to do if request is empty.
        if ($this->request === null || $this->request->getHost() === '') {
            return;
        }

        $requestUrl = parse_url($this->request->getSchemeAndHttpHost());

        $configCanonical = (string) $this->config->get('general/canonical', $this->getRequest()->getSchemeAndHttpHost());

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

    public function get(?string $route = null, array $params = [], bool $absolute = true): ?string
    {
        // Ensure request has been matched
        if (! $this->getRequest()->attributes->get('_route')) {
            return null;
        }

        $path = $this->getPath();

        if ($route) {
            $path = $this->returnPath($route, $params);
        }

        if (! $absolute) {
            return $path;
        }

        return sprintf(
            '%s://%s%s%s',
            $this->getScheme(),
            $this->getHost(),
            ($this->getPort() ? ':' . $this->getPort() : ''),
            $path
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
        $this->scheme = mb_trim($scheme, ':/');
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
            $route = $this->getRequest()->attributes->get('_route');
            $params = $this->getRequest()->attributes->get('_route_params');

            $this->path = $this->generateLink($route, $params, false);
        }

        return $this->path;
    }

    public function setPath(?string $route = null, array $params = []): void
    {
        $this->path = $this->returnPath($route, $params);
    }

    public function returnPath(?string $route = null, array $params = []): string
    {
        if (! $route && ! $this->getRequest()->attributes->has('_route')) {
            return '';
        } elseif (! $route) {
            $route = $this->getRequest()->attributes->get('_route');
        }

        return $this->generateLink($route, $params, false);
    }

    /**
     * Decorated by `\Bolt\Utils\CanonicalCacher`
     */
    public function generateLink(?string $route, ?array $params, $canonical = false): ?string
    {
        $removeDefaultLocaleOnCanonical = $this->config->get('general/localization/remove_default_locale_on_canonical', true);
        $hasDefaultLocale = isset($params['_locale']) && $params['_locale'] === $this->defaultLocale;

        if ($removeDefaultLocaleOnCanonical && $hasDefaultLocale) {
            unset($params['_locale']);
            $routeWithoutLocale = str_replace('_locale', '', $route);

            // If a route without the locale exists, use that. e.g. record_locale -> record
            try {
                $this->generateLink($routeWithoutLocale, $params);
                $route = $routeWithoutLocale;
            } catch (RouteNotFoundException) {
            }
        }

        // If contentTypeSlug param is passed, but the given route does not require it, unset it
        // This ensures we do not end up with query parameter ?contentTypeSlug=entries in the generated URL
        if (isset($params['contentTypeSlug']) && ! $this->routeRequiresParam($route, 'contentTypeSlug')) {
            unset($params['contentTypeSlug']);
        }

        try {
            return $this->urlGenerator->generate(
                $route,
                $params,
                $canonical ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH
            );
        } catch (InvalidParameterException | MissingMandatoryParametersException | RouteNotFoundException) {
            // Just use the current URL /shrug
            return $canonical ? $this->getRequest()->getUri() : $this->getRequest()->getPathInfo();
        }
    }

    private function routeRequiresParam(string $route, string $param): bool
    {
        $routes = $this->router->getRouteCollection();

        /** @var Route|null $routeDefinition */
        $routeDefinition = $routes->get($route);

        if (! $routeDefinition) {
            return false;
        }

        /** @var CompiledRoute $compiledRoute */
        $compiledRoute = $routeDefinition->compile();

        return in_array($param, $compiledRoute->getVariables(), true);
    }
}
