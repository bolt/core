<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Configuration\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    /** @var string */
    private $defaultLocale;

    public function __construct(Config $config, UrlGeneratorInterface $urlGenerator, RequestStack $requestStack, string $defaultLocale)
    {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->request = $requestStack->getCurrentRequest() ?? Request::createFromGlobals();
        $this->defaultLocale = $defaultLocale;

        $this->init();
    }

    public function init(): void
    {
        // Ensure in request cycle (even for override).
        if ($this->request === null || $this->request->getHost() === '') {
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

            $this->path = $this->generateLink($route, $params, false);
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

        $this->path = $this->generateLink($route, $params, false);
    }

    public function generateLink(?string $route, ?array $params, $canonical = false): ?string
    {
        if (isset($params['_locale']) && $params['_locale'] === $this->defaultLocale) {
            unset($params['_locale']);
            $routeWithoutLocale = str_replace('_locale', '', $route);

            // If a route without the locale exists, use that. e.g. record_locale -> record
            try {
                $this->generateLink($routeWithoutLocale, $params);
                $route = $routeWithoutLocale;
            } catch (RouteNotFoundException $e) {
            }
        }

        try {
            return $this->urlGenerator->generate(
                $route,
                $params,
                $canonical ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH
            );
        } catch (InvalidParameterException | MissingMandatoryParametersException | RouteNotFoundException | \TypeError $e) {
            // Just use the current URL /shrug
            return $canonical ? $this->request->getUri() : $this->request->getPathInfo();
        }
    }
}
