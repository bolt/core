<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use Bolt\Utils\PathCanonicalize;
use DateTimeImmutable;
use Exception;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class ImageController
{
    /**
     * The default maximum number of pixels of a generated image. ThumbnailHelper uses 10000 for a
     * missing width or height, so this allows e.g. a thumbnail of 4000 pixels wide.
     */
    private const MAX_IMAGE_SIZE = 4000 * 10000;

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

        $this->createServer($request);

        $thumbPath = $this->getThumbPath($request, $paramString, $filename);

        if ($thumbPath !== null) {
            // Normally the web server serves a saved thumbnail, but it can still reach PHP, e.g. when the
            // web server isn't configured for that, or when it was saved by a parallel request.
            if (! is_file($thumbPath)) {
                $this->saveThumb($filename, $thumbPath);
            }

            if (is_file($thumbPath)) {
                return $this->buildFileResponse($request, $thumbPath);
            }
        }

        return $this->buildResponse($request, $filename);
    }

    private function createServer(Request $request): void
    {
        // Width and height come from the URL, so limit the number of pixels of a generated image (larger
        // ones are scaled down), or a single request for e.g. 20000×20000 exhausts memory and CPU.
        $maxImageSize = (int) $this->config->get('general/thumbnails/max_image_size', self::MAX_IMAGE_SIZE);

        $this->server = ServerFactory::create([
            // With the request, Glide sets Last-Modified and responds to If-Modified-Since.
            'response' => new SymfonyResponseFactory($request),
            'source' => $this->getPath(),
            'cache' => $this->getPath('cache', true, 'thumbnails'),
            // Glide's cache key is the path relative to the location, so without a prefix per location,
            // e.g. `themes/foo.jpg` would be served from the cache for `files/foo.jpg`.
            'cache_path_prefix' => Path::makeRelative($this->getPath(), $this->config->getPath('web')),
            'max_image_size' => $maxImageSize > 0 ? $maxImageSize : self::MAX_IMAGE_SIZE,
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

    /**
     * Returns the path to save the thumbnail at, or null if it must not be saved.
     */
    private function getThumbPath(Request $request, string $paramString, string $filename): ?string
    {
        if (! $this->config->get('general/thumbnails/save_files', true)) {
            return null;
        }

        // A `?path=` query changes the image but not the URL path, so such thumbnails are not saved.
        if ($request->query->has('path')) {
            return null;
        }

        if (! $this->hasOnlyKnownParameters($paramString)) {
            return null;
        }

        // Never create hidden files or folders: a `thumbs/.htaccess/` folder makes Apache refuse every
        // request below `thumbs/`. This also rules out `.` and `..`.
        if (preg_match('#(^|/)\.#', $paramString . '/' . $filename)) {
            return null;
        }

        // The web server serves a saved thumbnail only if it lies at the path of its URL, so it is
        // stored under the requested parameter string (e.g. `400×300×80×crop`), not a normalized one.
        // That string comes from the URL: Path::join() resolves `.` and `..` segments (and `\` on
        // Windows), so only save the thumbnail if that did not change the path.
        $thumbsPath = Path::canonicalize($this->getPath('thumbs'));
        $thumbPath = Path::join($thumbsPath, $paramString, $filename);

        if ($thumbPath !== $thumbsPath . '/' . $paramString . '/' . $filename) {
            return null;
        }

        return $thumbPath;
    }

    private function saveThumb(string $filename, string $thumbPath): void
    {
        $filesystem = new Filesystem();
        $folderMode = $this->config->get('general/filepermissions/folders', 0775);
        $fileMode = $this->config->get('general/filepermissions/files', 0664);

        try {
            $imageBlob = $this->buildImage($filename);
            $filesystem->mkdir(dirname($thumbPath), $folderMode);
            $filesystem->dumpFile($thumbPath, $imageBlob);
            $filesystem->chmod($thumbPath, $fileMode);
        } catch (Throwable) {
            // Fail silently, output user-friendly exception elsewhere.
        }
    }

    /**
     * Generates a thumbnail to save. This bypasses Glide's cache, which would only keep a second copy
     * of the saved file.
     */
    private function buildImage(string $filename): string
    {
        // In case we're trying to "thumbnail" a svg, just return the whole thing.
        if ($this->isSvg($filename)) {
            $filepath = sprintf('%s%s%s', $this->getPath(), DIRECTORY_SEPARATOR, $filename);

            return file_get_contents($filepath);
        }

        if (! $this->server->sourceFileExists($filename)) {
            throw new FileNotFoundException(sprintf('Could not find the image `%s`.', $filename));
        }

        $source = $this->server->getSource()->read($this->server->getSourcePath($filename));

        return $this->server->getApi()->run($source, $this->server->getAllParams($this->parameters));
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
            return $this->allowCaching($request, $this->server->getImageResponse($filename, $this->parameters));
        } catch (FileNotFoundException) {
            return $this->sendErrorImage();
        }
    }

    /**
     * @return string[]
     */
    private function splitParameters(string $paramString): array
    {
        return explode('×', (string) preg_replace('/([0-9])(x)([0-9a-z])/i', '\1×\3', $paramString));
    }

    /**
     * Serves a saved thumbnail with the same caching headers as Glide's responses.
     */
    private function buildFileResponse(Request $request, string $path): Response
    {
        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', MimeTypes::getDefault()->getMimeTypes(Path::getExtension($path, true))[0] ?? 'application/octet-stream');
        $response->setMaxAge(31536000);
        $response->setExpires(new DateTimeImmutable('+1 year'));
        $response->isNotModified($request);

        return $this->allowCaching($request, $response);
    }

    /**
     * Symfony makes a response private and uncacheable as soon as the session is used, which the
     * remember-me authenticator of the firewall does on every request. Thumbnails don't depend on the
     * session, so keep their caching headers, unless the session was started (e.g. for a logged-in
     * user): the response could then set the session cookie, which must not be cached.
     */
    private function allowCaching(Request $request, Response $response): Response
    {
        if (! $request->hasSession() || ! $request->getSession()->isStarted()) {
            $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        }

        return $response;
    }

    private function parseParameters(string $paramString): void
    {
        $raw = $this->splitParameters($paramString);

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

    /**
     * Each parameter string is saved as a separate file, while parseParameters() ignores unknown or
     * superfluous segments. Only save thumbnails for parameter strings of which every segment is
     * understood, so `400×300×80×crop×files×<anything>` can't fill the disk with copies of the same
     * thumbnail. Segments are checked in the order parseParameters() reads them; a location has
     * already been checked by isAllowedSource().
     */
    private function hasOnlyKnownParameters(string $paramString): bool
    {
        $raw = $this->splitParameters($paramString);

        $isSize = fn (string $value): bool => (bool) preg_match('/^(0|[1-9]\d{0,4})$/', $value);
        $isQuality = fn (string $value): bool => (bool) preg_match('/^(0|[1-9]\d?|100)$/', $value);
        $isFit = fn (string $value): bool => $this->isKnownFit($this->parseFit($value));

        if (count($raw) > 5 || ! $isSize($raw[0]) || (isset($raw[1]) && ! $isSize($raw[1]))) {
            return false;
        }

        if (isset($raw[2]) && ! $isQuality($raw[2]) && ! $isFit($raw[2])) {
            return false;
        }

        // Without a fifth segment, the fourth one is a location if it doesn't look like a fit.
        if (isset($raw[3]) && (isset($raw[4]) || $this->testFit($this->parseFit($raw[3])))) {
            return $isFit($raw[3]);
        }

        return true;
    }

    /**
     * The fits Glide understands (see League\Glide\Manipulators\Size::getFit()), each in one form only.
     */
    private function isKnownFit(string $fit): bool
    {
        return (bool) preg_match(
            '/^(contain|max|fill|fill-max|stretch|(crop|cover)(-(top-left|top|top-right|left|center|right|bottom-left|bottom|bottom-right))?|crop-\d{1,3}-\d{1,3}(-\d{1,3}(\.\d{1,2})?)?)$/',
            $fit
        );
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
