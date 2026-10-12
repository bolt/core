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

        // The location is part of the parameters, so they must be parsed before the filename is checked.
        $this->parseParameters($paramString);

        try {
            $filename = PathCanonicalize::canonicalize($this->getPath(), $filename, true);
        } catch (Exception) {
            return $this->sendErrorImage();
        }

        if (! $this->isAllowedSource($filename)) {
            return $this->sendErrorImage();
        }

        $this->createServer();
        $this->saveThumb($request, $paramString, $filename);

        return $this->buildResponse($request, $filename);
    }

    private function createServer(): void
    {
        $this->server = ServerFactory::create([
            'response' => new SymfonyResponseFactory(),
            'source' => $this->getPath(),
            'cache' => $this->getPath('cache', true, 'thumbnails'),
            // Glide's cache key is the path relative to the location, so without a prefix per location,
            // e.g. `themes/foo.jpg` would be served from the cache for `files/foo.jpg`.
            'cache_path_prefix' => Path::makeRelative($this->getPath(), $this->config->getPath('web')),
        ]);
    }

    private function getLocation(): string
    {
        return $this->parameters['location'] ?? 'files';
    }

    private function getPath(?string $path = null, bool $absolute = true, $additional = null): string
    {
        if (! $path) {
            $path = $this->getLocation();
        }

        return $this->config->getPath($path, $absolute, $additional);
    }

    /**
     * The location comes from the URL and is resolved like any path, so `site`, `config` or `..`
     * would read images from anywhere in (or above) the project. Only read images from locations
     * inside the web root, like `files` and `themes`, which are public anyway, and never from the
     * `thumbs` folder, so no thumbnails are made of thumbnails.
     */
    private function isAllowedSource(string $filename): bool
    {
        $locationPath = $this->getPath();
        $sourcePath = Path::join($locationPath, $filename);

        return Path::isBasePath($this->config->getPath('web'), $locationPath)
            && ! Path::isBasePath($this->config->getPath('thumbs'), $sourcePath);
    }

    private function saveThumb(Request $request, string $paramString, string $filename): void
    {
        if (! $this->config->get('general/thumbnails/save_files', true)) {
            return;
        }

        // A `?path=` query changes the image but not the URL path, so such thumbnails are not saved.
        if ($request->query->has('path')) {
            return;
        }

        // Never create hidden files or folders: a `thumbs/.htaccess/` folder makes Apache refuse every
        // request below `thumbs/`. This also rules out `.` and `..`.
        if (preg_match('#(^|/)\.#', $paramString . '/' . $filename)) {
            return;
        }

        // The web server serves a saved thumbnail only if it lies at the path of its URL, so it is
        // stored under the requested parameter string (e.g. `400×300×80×crop`), not a normalized one.
        // That string comes from the URL: Path::join() resolves `.` and `..` segments (and `\` on
        // Windows), so only save the thumbnail if that did not change the path.
        $thumbsPath = Path::canonicalize($this->getPath('thumbs'));
        $thumbPath = Path::join($thumbsPath, $paramString, $filename);

        if ($thumbPath !== $thumbsPath . '/' . $paramString . '/' . $filename) {
            return;
        }

        $filesystem = new Filesystem();
        $folderMode = $this->config->get('general/filepermissions/folders', 0775);
        $fileMode = $this->config->get('general/filepermissions/files', 0664);

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
            $filepath = sprintf('%s%s%s', $this->getPath(), DIRECTORY_SEPARATOR, $filename);

            return file_get_contents($filepath);
        }

        if ($request->query->has('path')) {
            $filename = sprintf('%s/%s', $request->query->getString('path'), $filename);
        }

        // Glide serves an image from its cache without checking that the source still exists.
        if (! $this->server->sourceFileExists($filename)) {
            throw new FileNotFoundException(sprintf('Could not find the image `%s`.', $filename));
        }

        $cacheFile = $this->server->makeImage($filename, $this->parameters);

        return $this->server->getCache()->read($cacheFile);
    }

    private function buildResponse(Request $request, string $filename): Response
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

        if ($request->query->has('path')) {
            $filename = sprintf('%s/%s', $request->query->getString('path'), $filename);
        }

        try {
            return $this->server->getImageResponse($filename, $this->parameters);
        } catch (FileNotFoundException) {
            return $this->sendErrorImage();
        }
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
            $possibleFit = $this->parseFit($raw[3]);

            if ($this->testFit($possibleFit)) {
                $this->parameters['fit'] = $possibleFit;
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
