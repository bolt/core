<?php


namespace Bolt;


use Bolt\Configuration\Config;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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

    /** @var string */
    private $port = null;

    /** @var string */
    private $path = null;

    public function __construct(Config $config, UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->request = $requestStack->getCurrentRequest();

        $this->init();
    }

    public function init()
    {
        // Ensure in request cycle (even for override).
        if ($this->request === null) {
            return null;
        }

        $requestUrl = parse_url($this->request->getSchemeAndHttpHost());

        $configCanonical = $this->config->get('general/canonical', $this->request->getSchemeAndHttpHost());
//        dd($this->request->getSchemeAndHttpHost(), $configCanonical);

        if (strpos($configCanonical, 'http') !== 0) {
            $configCanonical = $requestUrl['scheme'] . '://' . $configCanonical;
        }

        $configUrl = parse_url($configCanonical);


        $this->setScheme($configUrl['scheme']);
        $this->setHost($configUrl['host']);
        $this->setPort($configUrl['port'] ?? null);

    }


    public function get()
    {
        // Ensure request has been matched
        if (!$this->request->attributes->get('_route')) {
            return null;
        }

        $url = sprintf("%s://%s%s%s",
            $this->getScheme(),
            $this->getHost(),
            ($this->getPort() ? ':' . $this->getPort() : ''),
            $this->getPath()
        );

        return $url;

    }

    /**
     * Override the initial UrlGeneratorInterface
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Override the scheme
     */
    public function setScheme(string $scheme): void
    {
        $this->scheme = trim($scheme, ':/');
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }


    /**
     * @return string
     */
    public function getPort(): ?string
    {
        return $this->port;
    }

    /**
     * @param string $port
     */
    public function setPort(?string $port): void
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        if ($this->path === null) {
            $this->path = $this->urlGenerator->generate(
                $this->request->attributes->get('_route'),
                $this->request->attributes->get('_route_params'),
                UrlGeneratorInterface::ABSOLUTE_PATH);
        }

        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}