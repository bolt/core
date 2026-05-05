<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use Bolt\Utils\PathCanonicalize;
use Exception;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class ImageController
{
    private const SUPPORTED_FORMATS = ['jpg', 'webp', 'png', 'gif', 'avif'];

    private Server $server;

    /**
     * @var array{
     *     w?: int,
     *     h?: int,
     *     fit?: string,
     *     location?: string,
     *     q?: int
     * }
     */
    private array $parameters = [];

    public function __construct(
        private readonly Config $config
    ) {
    }

    #[Route(path: '/thumbs/{paramString}/{filename}', name: 'thumbnail', requirements: ['filename' => '.+'], methods: [Request::METHOD_GET])]
    public function thumbnail(Request $request, string $paramString, string $filename): Response
    {
        if (! $this->isImage($filename)) {
            return $this->sendErrorImage();
        }

        $this->parseParameters($paramString);
        $urlFilename = $filename;
        $sourceFilename = $this->parseFormatFromFilename($filename);

        try {
            $sourceFilename = PathCanonicalize::canonicalize($this->getPath($request), $sourceFilename, true);
        } catch (Exception) {
            return $this->sendErrorImage();
        }

        $this->createServer($request);
        $this->saveThumb($request, $sourceFilename, $urlFilename);

        return $this->buildResponse($request, $sourceFilename);
    }

    private function createServer(Request $request): void
    {
        $this->server = ServerFactory::create([
            'response' => new SymfonyResponseFactory(),
            'source' => $this->getPath($request),
            'cache' => $this->getPath($request, 'cache', true, 'thumbnails'),
        ]);
    }

    private function getLocation(Request $request): string
    {
        return $this->parameters['location']
            ?? $request->query->getString('location', 'files') ?? 'files';
    }

    private function getPath(Request $request, ?string $path = null, bool $absolute = true, $additional = null): string
    {
        if (! $path) {
            $path = $this->getLocation($request);
        }

        return $this->config->getPath($path, $absolute, $additional);
    }

    private function parseFormatFromFilename(string $filename): string
    {
        $ext = mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($this->isSupportedFormat($ext) && pathinfo(pathinfo($filename, PATHINFO_FILENAME), PATHINFO_EXTENSION) !== '') {
            $this->parameters['fm'] = $ext;

            return mb_substr($filename, 0, -(mb_strlen($ext) + 1));
        }

        return $filename;
    }

    private function saveThumb(Request $request, string $filename, string $urlFilename = ''): void
    {
        if (! $this->config->get('general/thumbnails/save_files', true)) {
            return;
        }

        $filesystem = new Filesystem();
        $folderMode = $this->config->get('general/filepermissions/folders', 0775);
        $fileMode = $this->config->get('general/filepermissions/files', 0664);

        $thumbPath = Path::join(
            $this->getPath($request, 'thumbs'),
            $this->parameterPath(),
            $urlFilename ?: $filename
        );

        try {
            $imageBlob = $this->buildImage($request, $filename);
            $filesystem->mkdir(dirname($thumbPath), $folderMode);
            $filesystem->dumpFile($thumbPath, $imageBlob);
            $filesystem->chmod($thumbPath, $fileMode);
        } catch (Throwable) {
            // Fail silently, output user-friendly exception elsewhere.
        }
    }

    private function buildImage(Request $request, string $filename): string
    {
        // In case we're trying to "thumbnail" a svg, just return the whole thing.
        if ($this->isSvg($filename)) {
            $filepath = sprintf('%s%s%s', $this->getPath($request), DIRECTORY_SEPARATOR, $filename);

            return file_get_contents($filepath);
        }

        if ($request->query->has('path')) {
            $filename = sprintf('%s/%s', $request->query->getString('path'), $filename);
        }

        $cacheFile = $this->server->makeImage($filename, $this->parameters);

        return $this->server->getCache()->read($cacheFile);
    }

    private function buildResponse(Request $request, string $filename): Response
    {
        $filepath = $this->getPath($request, null, false, $filename);

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

        if ($request->query->has('path')) {
            $filename = sprintf('%s/%s', $request->query->getString('path'), $filename);
        }

        try {
            return $this->server->getImageResponse($filename, $this->parameters);
        } catch (FileNotFoundException) {
            return $this->sendErrorImage();
        }
    }

    private function parseParametersold(string $paramString): void
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
            $possibleFit = $this->parseFit($raw[3]);

            if ($this->testFit($possibleFit)) {
                $this->parameters['fit'] = $possibleFit;
            } else {
                $this->parameters['location'] = $raw[3];
            }
        }
    }

    private function parseParameters(string $paramString): void
    {
        $raw = explode('×', (string) preg_replace('/([0-9])(x)([0-9a-z])/i', '\1×\3', $paramString));
        $defaultFit = $this->config->get('general/thumbnails/default_cropping', 'default');

        $this->parameters = [
            'w' => (isset($raw[0]) && is_numeric($raw[0])) ? (int) $raw[0] : 400,
            'h' => (isset($raw[1]) && is_numeric($raw[1])) ? (int) $raw[1] : 300,
            'fm' => '',
            'fit' => '',
            'location' => 'files',
            'q' => 80,
        ];

        $remaining = array_values(array_filter(
            array_slice($raw, 2),
            static fn (int|string $value): bool => $value !== ''
        ));

        if (isset($remaining[0]) && is_numeric($remaining[0]) && 0 <= (int) $remaining[0] && (int) $remaining[0] <= 100) {
            $this->parameters['q'] = (int) array_shift($remaining);
        }

        foreach ($remaining as $token) {
            $token = (string) $token;
            $normalizedToken = mb_strtolower($token);

            if ($this->parameters['fm'] === '' && $this->isSupportedFormat($normalizedToken)) {
                $this->parameters['fm'] = $normalizedToken;
                continue;
            }

            $fit = $this->parseFit($normalizedToken);
            if ($this->testFit($fit)) {
                $this->parameters['fit'] = $fit;
                continue;
            }

            if ($this->parameters['location'] === 'files') {
                $this->parameters['location'] = $token;
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

    private function isSupportedFormat(string $format): bool
    {
        return in_array($format, self::SUPPORTED_FORMATS, true);
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

    private function parameterPath(): string
    {
        $parts = array_filter([
            $this->parameters['w'] ?? 0,
            $this->parameters['h'] ?? 0,
            $this->parameters['q'] ?? 80,
            $this->parameters['fit'] ?? null,
            $this->parameters['location'] ?? 'files',
        ], fn (int|string|null $v): bool => $v !== null && $v !== '' && $v !== 0);

        return implode('×', $parts);
    }

    public function sendErrorImage(): Response
    {
        $image404Path = dirname(__DIR__, 2) . '/assets/static/images/404-image.png';
        $response = new Response(file_get_contents($image404Path));
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }
}
