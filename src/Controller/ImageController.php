<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class ImageController
{
    /** @var Server */
    private $server;

    private array $parameters = [];

    /** @var Request */
    private $request;

    public function __construct(
        private readonly Config $config,
        RequestStack $requestStack
    ) {
        $this->request = $requestStack->getCurrentRequest();
    }

    #[Route(path: '/thumbs/{paramString}/{filename}', name: 'thumbnail', requirements: ['filename' => '.+'], methods: ['GET'])]
    public function thumbnail(string $paramString, string $filename): Response
    {
        if (! $this->isImage($filename)) {
            return $this->sendErrorImage();
        }

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
        return $this->parameters['location'] ?? $this->request->query->get('location', 'files');
    }

    private function getPath(?string $path = null, bool $absolute = true, $additional = null): string
    {
        if (! $path) {
            $path = $this->getLocation();
        }

        return $this->config->getPath($path, $absolute, $additional);
    }

    private function saveAsFile(string $paramString, string $filename): void
    {
        if (! $this->config->get('general/thumbnails/save_files', true)) {
            return;
        }

        $filesystem = new Filesystem();
        $filePath = sprintf('%s%s%s%s%s', $this->getPath('thumbs'), DIRECTORY_SEPARATOR, $paramString, DIRECTORY_SEPARATOR, $filename);
        $folderMode = $this->config->get('general/filepermissions/folders', 0775);
        $fileMode = $this->config->get('general/filepermissions/files', 0664);

        try {
            $imageBlob = $this->buildImage($filename);
            $filesystem->mkdir(dirname($filePath), $folderMode);
            $filesystem->dumpFile($filePath, $imageBlob);
            $filesystem->chmod($filePath, $fileMode);
        } catch (Throwable) {
            // Fail silently, output user-friendly exception elsewhere.
        }
    }

    private function buildImage(string $filename): string
    {
        // In case we're trying to "thumbnail" an svg, just return the whole thing.
        if ($this->isSvg($filename)) {
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
        $filepath = $this->getPath(null, false, $filename);

        if (! (new Filesystem())->exists($filepath)) {
            // $notice = sprintf("The file '%s' does not exist.", $filepath);

            return $this->sendErrorImage();
        }

        // In case we're trying to "thumbnail" an svg, just return the whole thing.
        if ($this->isSvg($filename)) {
            $response = new Response(file_get_contents($filepath));
            $response->headers->set('Content-Type', 'image/svg+xml');

            return $response;
        }

        if ($this->request->query->has('path')) {
            $filename = sprintf('%s/%s', $this->request->query->get('path'), $filename);
        }

        return $this->server->getImageResponse($filename, $this->parameters);
    }

    private function parseParameters(string $paramString): void
    {
        $raw = explode('×', (string) preg_replace('/([0-9])(x)([0-9a-z])/i', '\1×\3', $paramString));

        $this->parameters = [
            'w' => (isset($raw[0]) && is_numeric($raw[0])) ? (int) $raw[0] : 400,
            'h' => (isset($raw[1]) && is_numeric($raw[1])) ? (int) $raw[1] : 300,
            'fit' => $raw[2] ?? $this->config->get('general/thumbnails/default_cropping', 'default'),
            'location' => 'files',
            'q' => (! empty($raw[2]) && 0 <= $raw[2] && $raw[2] <= 100) ? (int) $raw[2] : 80,
        ];

        if (isset($raw[4])) {
            $this->parameters['fit'] = $this->parseFit($raw[3]);
            $this->parameters['location'] = $raw[4];
        } elseif (isset($raw[3])) {
            $posible_fit = $this->parseFit($raw[3]);

            if ($this->testFit($posible_fit)) {
                $this->parameters['fit'] = $posible_fit;
            } else {
                $this->parameters['location'] = $raw[3];
            }
        }
    }

    private function isSvg(string $filename): bool
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return $extension === 'svg';
    }

    private function isImage(string $filename): bool
    {
        $pathinfo = pathinfo($filename);

        $imageExtensions = ['gif', 'png', 'jpg', 'jpeg', 'svg', 'avif', 'webp'];
        $ext = mb_strtolower($pathinfo['extension']);

        return array_key_exists('extension', $pathinfo) && in_array($ext, $imageExtensions, true);
    }

    private function testFit(string $fit): bool
    {
        return (bool) preg_match('/^(contain|max|fill|stretch|crop)(-.+)?/', $fit);
    }

    public function parseFit(string $fit): string
    {
        return match ($fit) {
            'n', 'contain', 'default' => 'contain',
            'm', 'max' => 'max',
            'f', 'fill' => 'fill',
            's', 'stretch' => 'stretch',
            'c', 'crop' => 'crop',
            default => $fit,
        };
    }

    public function sendErrorImage(): Response
    {
        $image404Path = dirname(__DIR__, 2) . '/assets/static/images/404-image.png';
        $response = new Response(file_get_contents($image404Path));
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }
}
