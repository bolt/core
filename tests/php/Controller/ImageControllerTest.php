<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
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
 * possible to read or write files outside the `files` and `thumbs` folders through them.
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
    }

    #[DataProvider('thumbnailUrlProvider')]
    public function testThumbnailIsSavedAtThePathOfItsUrl(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', self::encode($url));

        $this->assertResponseIsSuccessful();

        $savedFile = self::PUBLIC_DIR . rawurldecode($url);
        $this->assertFileExists($savedFile, 'The web server can only serve a saved thumbnail from the path of its URL.');
        $this->assertSame($client->getInternalResponse()->getContent(), file_get_contents($savedFile));
    }

    public static function unsavedThumbnailProvider(): iterable
    {
        yield 'parent folder as parameters' => ['/thumbs/../kitten.jpg'];
        yield 'encoded parent folder as parameters' => ['/thumbs/%2E%2E/kitten.jpg'];
        yield 'current folder as parameters' => ['/thumbs/./kitten.jpg'];
        yield 'hidden folder as parameters (Apache refuses requests below a .htaccess folder)' => ['/thumbs/.htaccess/kitten.jpg'];
        yield 'path query (changes the image, not the URL path)' => ['/thumbs/129×94×80/kitten.jpg?path='];
        yield 'missing source image' => ['/thumbs/130×94×80/does-not-exist.jpg'];
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
        yield 'location query' => ['/thumbs/132×94/assets/static/images/placeholder.png?location=site'];
        yield 'project root as location' => ['/thumbs/132×94×80×crop×site/assets/static/images/placeholder.png'];
        yield 'svg, project root as location' => ['/thumbs/132×94×80×crop×site/assets/static/images/bolt_logo_dashboard.svg'];
        yield 'project root as location without fit' => ['/thumbs/132×94×80×site/assets/static/images/placeholder.png'];
        yield 'project root as location alias' => ['/thumbs/132×94×80×crop×%25site%25/assets/static/images/placeholder.png'];
        yield 'project root as relative location' => ['/thumbs/132×94×80×crop×./assets/static/images/placeholder.png'];
        yield 'parent of the project as location' => ['/thumbs/132×94×80×crop×../' . basename(dirname(__DIR__, 3)) . '/assets/static/images/placeholder.png'];
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
     * Thumbnails are not made of thumbnails: through the `thumbs` location, or through a location
     * that contains the `thumbs` folder.
     */
    public function testThumbnailOfAThumbnailIsNotServed(): void
    {
        $client = static::createClient();
        $client->request('GET', self::encode('/thumbs/137×94/kitten.jpg'));
        $this->assertFileExists(self::PUBLIC_DIR . '/thumbs/137×94/kitten.jpg');
        $thumbsEntries = $this->entries(self::PUBLIC_DIR . '/thumbs');

        foreach (['/thumbs/138×94×80×crop×thumbs/137×94/kitten.jpg', '/thumbs/138×94×80×crop×web/thumbs/137×94/kitten.jpg'] as $url) {
            $client->request('GET', self::encode($url));

            $this->assertSame(sha1_file(self::ERROR_IMAGE), sha1($client->getInternalResponse()->getContent()), sprintf('Expected the error image for %s.', $url));
            $this->assertSame($thumbsEntries, $this->entries(self::PUBLIC_DIR . '/thumbs'));
        }
    }

    /**
     * Glide caches images by their path relative to the location, so the cache must be separate for
     * each location: an image that only exists in the theme must not be served from the cache, or
     * saved, for a URL with the `files` location.
     */
    public function testCachedImageOfAnotherLocationIsNotServed(): void
    {
        $client = static::createClient();
        $client->request('GET', self::encode('/thumbs/151×94×80×themes/image-controller-test/kitten.jpg'));
        $this->assertNotSame(sha1_file(self::ERROR_IMAGE), sha1($client->getInternalResponse()->getContent()));
        $thumbsEntries = $this->entries(self::PUBLIC_DIR . '/thumbs');

        $client->request('GET', self::encode('/thumbs/151×94×80/image-controller-test/kitten.jpg'));

        $this->assertSame(sha1_file(self::ERROR_IMAGE), sha1($client->getInternalResponse()->getContent()), 'Expected the error image.');
        $this->assertSame($thumbsEntries, $this->entries(self::PUBLIC_DIR . '/thumbs'));
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
