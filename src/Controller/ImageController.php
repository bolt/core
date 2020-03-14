<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Server;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController
{
    /** @var Config */
    private $config;

    private $thumbnailOptions = ['w', 'h', 'fit'];

    /** @var Server */
    private $server;

    /** @var array */
    private $parameters = [];

    /** @var Request */
    private $request;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/thumbs/{paramString}/{filename}", methods={"GET"}, name="thumbnail", requirements={"filename"=".+"})
     */
    public function thumbnail(string $paramString, string $filename, Request $request)
    {
        $this->request = $request;

        $this->parseParameters($paramString);
        $this->createServer();
        $this->saveAsFile($paramString, $filename);

        return $this->buildResponse($filename);
    }

    private function createServer(): void
    {
        $this->server = ServerFactory::create([
            'response' => new SymfonyResponseFactory(),
            'source' => $this->getPath(),
            'cache' => $this->getPath('cache', true, 'thumbnails'),
        ]);
    }

    private function getLocation(): string
    {
        return isset($this->parameters['location']) ? $this->parameters['location'] : $this->request->query->get('location', 'files');
    }

    private function getPath(string $path = null, bool $absolute = true, $additional = null): string
    {
        if (!$path) {
            $path = $this->getLocation();
        }

        return $this->config->getPath($path, $absolute, $additional);
    }

    private function saveAsFile(string $paramString, string $filename): void
    {
        if (! $this->config->get('general/thumbnails/save_files', true)) {
            return;
        }

        $filePath = sprintf('%s%s%s%s%s', $this->getPath('thumbs'), DIRECTORY_SEPARATOR, $paramString, DIRECTORY_SEPARATOR, $filename);

        $filesystem = new Filesystem();
        $filesystem->mkdir(dirname($filePath));
        $filesystem->dumpFile($filePath, $this->buildImage($filename));
    }

    private function buildImage(string $filename): string
    {
        // In case we're trying to "thumbnail" an svg, just return the whole thing.
        if (pathinfo($filename)['extension'] === 'svg') {
            $filepath = sprintf('%s%s%s', $this->getPath(), DIRECTORY_SEPARATOR, $filename);

            return file_get_contents($filepath);
        }

        if ($this->request->query->has('path')) {
            $filename = sprintf('%s/%s', $this->request->query->get('path'), $filename);
        }

        $cacheFile = $this->server->makeImage($filename, $this->parameters);

        return $this->server->getCache()->read($cacheFile);
    }

    private function buildResponse(string $filename): Response
    {
        // In case we're trying to "thumbnail" an svg, just return the whole thing.
        if (pathinfo($filename)['extension'] === 'svg') {
            $filepath = sprintf('%s%s%s', $this->getPath(), DIRECTORY_SEPARATOR, $filename);

            return new Response(file_get_contents($filepath));
        }

        if ($this->request->query->has('path')) {
            $filename = sprintf('%s/%s', $this->request->query->get('path'), $filename);
        }

        return $this->server->getImageResponse($filename, $this->parameters);
    }

    private function parseParameters(string $paramString): void
    {
        $raw = explode('×', $paramString);

        $this->parameters = [
            'w' => is_numeric($raw[0]) ? (int) $raw[0] : 400,
            'h' => !empty($raw[1]) && is_numeric($raw[1]) ? (int) $raw[1] : 300,
        ];

        foreach ($raw as $rawParameter) {
            if (strpos($rawParameter, '=') !== false) {
                [$key, $value] = explode('=', $rawParameter);

                // @todo Add more thumbnailing options here, perhaps.
                if (in_array($key, $this->thumbnailOptions)) {
                    $this->parameters[$key] = $value;
                }
            }
        }
    }
}
