<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller;

use Bolt\Configuration\Config;
use Bolt\Utils\ThumbnailHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * With `thumbnails: save_files: true` a generated thumbnail must be stored at the path of its URL,
 * so that the web server serves the next request for it straight from disk, without PHP: Apache
 * only rewrites to index.php if `%{REQUEST_FILENAME}` is not a file (see public/.htaccess), Nginx
 * does the same with `try_files $uri`.
 *
 * Both the parameters and the filename of a thumbnail URL are user input, so it must also not be
 * possible to write files outside the `thumbs` folder through them, or to read files outside the
 * `files` folder through the filename.
 */
class ImageControllerTest extends WebTestCase
{
    private const PUBLIC_DIR = __DIR__ . '/../../../public';
    private const ERROR_IMAGE = __DIR__ . '/../../../assets/static/images/404-image.png';

    /**
     * A folder in public/theme/ with an image, for thumbnails with the `themes` location.
     */
    private const THEME_IMAGE_DIR = self::PUBLIC_DIR . '/theme/image-controller-test';

    /** Files and folders in public/ that existed before the test. */
    private array $existingPublicEntries = [];

    /** Files and folders in public/thumbs/ (and below) that existed before the test. */
    private array $existingThumbsEntries = [];

    public static function setUpBeforeClass(): void
    {
        (new Filesystem())->copy(self::PUBLIC_DIR . '/files/kitten.jpg', self::THEME_IMAGE_DIR . '/kitten.jpg');
        (new Filesystem())->copy(__DIR__ . '/../../../assets/static/images/bolt_logo_dashboard.svg', self::THEME_IMAGE_DIR . '/logo.svg');
    }

    public static function tearDownAfterClass(): void
    {
        (new Filesystem())->remove(self::THEME_IMAGE_DIR);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->existingPublicEntries = $this->entries(self::PUBLIC_DIR, 0);
        $this->existingThumbsEntries = $this->entries(self::PUBLIC_DIR . '/thumbs');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove(array_map(
            fn (string $entry): string => self::PUBLIC_DIR . '/thumbs/' . $entry,
            array_diff($this->entries(self::PUBLIC_DIR . '/thumbs'), $this->existingThumbsEntries)
        ));

        parent::tearDown();
    }

    public static function thumbnailUrlProvider(): iterable
    {
        yield 'width, height, quality and fit' => ['/thumbs/123×94×80×crop/kitten.jpg'];
        yield 'short fit alias' => ['/thumbs/124×94×80×c/kitten.jpg'];
        yield 'width and height only' => ['/thumbs/125×94/kitten.jpg'];
        yield 'fit with focal point' => ['/thumbs/126×94×80×crop-50-30/kitten.jpg'];
        yield 'fit with focal point and zoom' => ['/thumbs/131×94×80×crop-50-30-1.3/kitten.jpg'];
        yield 'x as separator' => ['/thumbs/127x94x80/kitten.jpg'];
        yield 'files location' => ['/thumbs/133×94×80×crop×files/kitten.jpg'];
        yield 'themes location' => ['/thumbs/134×94×80×crop×themes/image-controller-test/kitten.jpg'];
        yield 'themes location without fit' => ['/thumbs/135×94×80×themes/image-controller-test/kitten.jpg'];
        yield 'web root as location' => ['/thumbs/136×94×80×crop×web/files/kitten.jpg'];
        yield 'fit without quality' => ['/thumbs/139×94×crop/kitten.jpg'];
        yield 'fit with position' => ['/thumbs/140×94×80×crop-top-left/kitten.jpg'];
        yield 'fit-max' => ['/thumbs/141×94×80×fill-max/kitten.jpg'];
        yield 'zero quality' => ['/thumbs/150×94×0/kitten.jpg'];
    }

    #[DataProvider('thumbnailUrlProvider')]
    public function testThumbnailIsSavedAtThePathOfItsUrl(string $url): void
    {
        $this->assertSavedAtThePathOfItsUrl(static::createClient(), $url);
    }

    /**
     * @return iterable<string, array{array<string, int|string|null>}>
     */
    public static function thumbnailHelperProvider(): iterable
    {
        yield 'width and height' => [['width' => 142, 'height' => 94]];
        yield 'width only' => [['width' => 143]];
        yield 'height only' => [['height' => 94]];
        yield 'fit' => [['width' => 144, 'height' => 94, 'fit' => 'crop']];
        yield 'fit alias' => [['width' => 145, 'height' => 94, 'fit' => 'default']];
        yield 'fit with focal point and zoom' => [['width' => 146, 'height' => 94, 'fit' => 'crop-50-30-1.3']];
        yield 'files location' => [['width' => 147, 'height' => 94, 'location' => 'files', 'fit' => 'crop']];
        yield 'themes location' => [['width' => 148, 'height' => 94, 'location' => 'themes', 'path' => 'image-controller-test']];
        yield 'themes location and fit' => [['width' => 149, 'height' => 94, 'location' => 'themes', 'path' => 'image-controller-test', 'fit' => 'crop']];
    }

    /**
     * Every thumbnail URL that Bolt generates must be saved.
     *
     * @param array<string, int|string|null> $arguments
     */
    #[DataProvider('thumbnailHelperProvider')]
    public function testThumbnailWithUrlFromThumbnailHelperIsSaved(array $arguments): void
    {
        $client = static::createClient();
        $url = (new ThumbnailHelper(static::getContainer()->get(Config::class)))->path('kitten.jpg', ...$arguments);

        $this->assertSavedAtThePathOfItsUrl($client, $url);
    }

    public static function unsavedThumbnailProvider(): iterable
    {
        yield 'parent folder as parameters' => ['/thumbs/../kitten.jpg'];
        yield 'encoded parent folder as parameters' => ['/thumbs/%2E%2E/kitten.jpg'];
        yield 'current folder as parameters' => ['/thumbs/./kitten.jpg'];
        yield 'hidden folder as parameters (Apache refuses requests below a .htaccess folder)' => ['/thumbs/.htaccess/kitten.jpg'];
        yield 'path query (changes the image, not the URL path)' => ['/thumbs/129×94×80/kitten.jpg?path='];
        yield 'missing source image' => ['/thumbs/130×94×80/does-not-exist.jpg'];
        yield 'unknown segment after the location' => ['/thumbs/130×94×80×crop×files×junk/kitten.jpg'];
        yield 'unknown fit' => ['/thumbs/130×94×junk/kitten.jpg'];
        yield 'unknown fit before the location' => ['/thumbs/130×94×80×junk×files/kitten.jpg'];
        yield 'unknown focal point' => ['/thumbs/130×94×80×crop-junk/kitten.jpg'];
        yield 'repeated focal point' => ['/thumbs/130×94×80×crop-50-30-50-30/kitten.jpg'];
        yield 'quality out of range' => ['/thumbs/130×94×101/kitten.jpg'];
        yield 'width with leading zero' => ['/thumbs/0130×94/kitten.jpg'];
        yield 'width with exponent' => ['/thumbs/1e2×94/kitten.jpg'];
        yield 'non-numeric width' => ['/thumbs/abc×94/kitten.jpg'];
        yield 'empty height' => ['/thumbs/130×/kitten.jpg'];
        // Glide decodes the filename a second time, so these read files/kitten.jpg.
        yield 'double encoded parent folder' => ['/thumbs/130×94/foo/%252e%252e/kitten.jpg'];
        yield 'double encoded parent folder, other prefix' => ['/thumbs/130×94/bar/%252e%252e/kitten.jpg'];
        yield 'double encoded character' => ['/thumbs/130×94/kitte%256e.jpg'];
    }

    #[DataProvider('unsavedThumbnailProvider')]
    public function testThumbnailIsNotSaved(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', self::encode($url));

        $this->assertResponseIsSuccessful();
        $this->assertNothingSaved();
    }

    /**
     * Each URL tries to reach an image in assets/static/images/ in the project root, outside the
     * `files` folder (and outside the web root). SVGs are read directly instead of through Glide.
     */
    public static function pathTraversalProvider(): iterable
    {
        yield 'svg, parent folders' => ['/thumbs/132×94/../../assets/static/images/bolt_logo_dashboard.svg'];
        yield 'svg, encoded slashes' => ['/thumbs/132×94/..%2F..%2Fassets%2Fstatic%2Fimages%2Fbolt_logo_dashboard.svg'];
        yield 'parent folders' => ['/thumbs/132×94/../../assets/static/images/placeholder.png'];
        yield 'parent folders after a subfolder' => ['/thumbs/132×94/foo/../../../assets/static/images/placeholder.png'];
        yield 'encoded dots' => ['/thumbs/132×94/%2E%2E/%2E%2E/assets/static/images/placeholder.png'];
        yield 'encoded slashes' => ['/thumbs/132×94/..%2F..%2Fassets%2Fstatic%2Fimages%2Fplaceholder.png'];
        yield 'double encoded dots' => ['/thumbs/132×94/%252E%252E/%252E%252E/assets/static/images/placeholder.png'];
        yield 'backslashes' => ['/thumbs/132×94/..%5C..%5Cassets%5Cstatic%5Cimages%5Cplaceholder.png'];
        yield 'path query' => ['/thumbs/132×94/placeholder.png?path=../../assets/static/images'];
    }

    #[DataProvider('pathTraversalProvider')]
    public function testImageOutsideTheFilesFolderIsNotServed(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', self::encode($url));

        $this->assertResponseIsSuccessful();
        $this->assertSame(sha1_file(self::ERROR_IMAGE), sha1($client->getInternalResponse()->getContent()), 'Expected the error image.');
        $this->assertNothingSaved();
    }

    /**
     * Glide keeps a generated image in its cache, also after the image itself is deleted (e.g. over
     * FTP). Its thumbnail must not be saved again from that cache, or the web server serves it.
     */
    public function testThumbnailOfADeletedImageIsNotSavedAgain(): void
    {
        $image = self::PUBLIC_DIR . '/files/image-controller-test-deleted.jpg';
        $url = '/thumbs/160×94/image-controller-test-deleted.jpg';
        (new Filesystem())->copy(self::PUBLIC_DIR . '/files/kitten.jpg', $image);
        $client = static::createClient();
        $client->request('GET', self::encode($url));
        $this->assertFileExists(self::PUBLIC_DIR . $url);

        (new Filesystem())->remove([$image, self::PUBLIC_DIR . $url]);
        $client->request('GET', self::encode($url));

        $this->assertSame(sha1_file(self::ERROR_IMAGE), sha1($client->getInternalResponse()->getContent()), 'Expected the error image.');
        $this->assertFileDoesNotExist(self::PUBLIC_DIR . $url);
    }

    /**
     * Normally the web server serves a saved thumbnail, but if the request still reaches PHP (e.g.
     * without `try_files` in Nginx), the saved file is served instead of generating it again.
     */
    public function testSavedThumbnailIsServed(): void
    {
        $url = '/thumbs/153×94/kitten.jpg';
        $savedFile = self::PUBLIC_DIR . $url;
        $savedImage = file_get_contents(self::PUBLIC_DIR . '/files/kitten2.jpg');
        (new Filesystem())->dumpFile($savedFile, $savedImage);
        touch($savedFile, $modified = time() - 3600);

        $client = static::createClient();
        $client->request('GET', self::encode($url));

        $this->assertResponseIsSuccessful();
        $this->assertSame($savedImage, $client->getInternalResponse()->getContent());
        $this->assertSame($savedImage, file_get_contents($savedFile), 'Expected the saved thumbnail not to be generated again.');
        $this->assertResponseHeaderSame('Content-Type', 'image/jpeg');
        $this->assertResponseHeaderSame('Last-Modified', gmdate('D, d M Y H:i:s', $modified) . ' GMT');
        $this->assertResponseHasHeader('ETag');
        $etag = $client->getResponse()->headers->get('ETag');

        $client->request('GET', self::encode($url), server: ['HTTP_IF_MODIFIED_SINCE' => gmdate('D, d M Y H:i:s', $modified) . ' GMT']);

        $this->assertResponseStatusCodeSame(304);
        $this->assertSame('', $client->getInternalResponse()->getContent());

        $client->request('GET', self::encode($url), server: ['HTTP_IF_NONE_MATCH' => $etag]);

        $this->assertResponseStatusCodeSame(304);
    }

    /**
     * A saved thumbnail is served with the headers the web server sends for any image, e.g. in
     * `files/`: validators, but no lifetime. That is also the case for the request that generates it,
     * and for anonymous visitors, although the firewall uses the session on every request.
     */
    public function testThumbnailHasTheHeadersOfAnImageFile(): void
    {
        $url = '/thumbs/155×94/kitten.jpg';
        $client = static::createClient();
        $client->request('GET', self::encode($url));
        $this->assertFileExists(self::PUBLIC_DIR . $url);

        $generated = $client->getResponse()->headers;

        $client->request('GET', self::encode($url));
        $saved = $client->getResponse()->headers;

        foreach ([$generated, $saved] as $headers) {
            $this->assertSame('public', $headers->get('Cache-Control'));
            $this->assertFalse($headers->has('Expires'));
            $this->assertSame('image/jpeg', $headers->get('Content-Type'));
            $this->assertSame(gmdate('D, d M Y H:i:s', filemtime(self::PUBLIC_DIR . $url)) . ' GMT', $headers->get('Last-Modified'));
            $this->assertNotNull($headers->get('ETag'));
            $this->assertSame([], $headers->getCookies());
        }
        $this->assertSame($generated->get('ETag'), $saved->get('ETag'));
    }

    /**
     * A response that may set the session cookie must not be cached.
     */
    public function testThumbnailIsNotCacheableWithStartedSession(): void
    {
        $client = static::createClient();
        $client->request('GET', self::encode('/thumbs/158×94/kitten.jpg?_locale=en'));

        $this->assertResponseIsSuccessful();
        $this->assertFileExists(self::PUBLIC_DIR . '/thumbs/158×94/kitten.jpg');
        $this->assertNotEmpty($client->getResponse()->headers->getCookies(), 'Expected the response to set the session cookie.');
        $this->assertFalse($client->getResponse()->headers->hasCacheControlDirective('public'));
        $this->assertTrue($client->getResponse()->headers->hasCacheControlDirective('private'));
    }

    public function testSavedSvgIsServedAsSvg(): void
    {
        $url = '/thumbs/154×94×80×themes/image-controller-test/logo.svg';
        $client = static::createClient();
        $client->request('GET', self::encode($url));
        $this->assertFileExists(self::PUBLIC_DIR . $url);

        $client->request('GET', self::encode($url));

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'image/svg+xml');
        $this->assertSame(file_get_contents(self::THEME_IMAGE_DIR . '/logo.svg'), $client->getInternalResponse()->getContent());
    }

    private function assertSavedAtThePathOfItsUrl(KernelBrowser $client, string $url): void
    {
        $client->request('GET', self::encode($url));

        $this->assertResponseIsSuccessful();

        $savedFile = self::PUBLIC_DIR . rawurldecode($url);
        $this->assertFileExists($savedFile, 'The web server can only serve a saved thumbnail from the path of its URL.');
        $this->assertSame($client->getInternalResponse()->getContent(), file_get_contents($savedFile));
    }

    private function assertNothingSaved(): void
    {
        $this->assertSame($this->existingPublicEntries, $this->entries(self::PUBLIC_DIR, 0));
        $this->assertSame($this->existingThumbsEntries, $this->entries(self::PUBLIC_DIR . '/thumbs'));
    }

    /**
     * Browsers send the `×` of thumbnail URLs percent-encoded.
     */
    private static function encode(string $url): string
    {
        return str_replace('×', rawurlencode('×'), $url);
    }

    /**
     * @return string[] the paths of the files and folders in `$folder`, relative to it
     */
    private function entries(string $folder, ?int $depth = null): array
    {
        $finder = (new Finder())->in($folder)->ignoreDotFiles(false);
        if ($depth !== null) {
            $finder->depth($depth);
        }

        $entries = [];
        foreach ($finder as $entry) {
            $entries[] = $entry->getRelativePathname();
        }
        sort($entries);

        return $entries;
    }
}
