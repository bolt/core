<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * With `thumbnails: save_files: true` a generated thumbnail must be stored at the path of its URL,
 * so that the web server serves the next request for it straight from disk, without PHP.
 */
class ImageControllerTest extends WebTestCase
{
    private const PUBLIC_DIR = __DIR__ . '/../../../public';

    /** Folders below public/thumbs/ that existed before the test. */
    private array $existingThumbFolders = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->existingThumbFolders = $this->thumbFolders();
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove(array_map(
            fn (string $folder): string => self::PUBLIC_DIR . '/thumbs/' . $folder,
            array_diff($this->thumbFolders(), $this->existingThumbFolders)
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
        yield 'path query (changes the image, not the URL path)' => ['/thumbs/129×94×80/kitten.jpg?path='];
        yield 'missing source image' => ['/thumbs/130×94×80/does-not-exist.jpg'];
    }

    #[DataProvider('unsavedThumbnailProvider')]
    public function testThumbnailIsNotSaved(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', self::encode($url));

        $this->assertResponseIsSuccessful();
        $this->assertFileDoesNotExist(self::PUBLIC_DIR . '/kitten.jpg');
        $this->assertFileDoesNotExist(self::PUBLIC_DIR . '/thumbs/kitten.jpg');
        $this->assertSame($this->existingThumbFolders, $this->thumbFolders());
    }

    /**
     * Browsers send the `×` of thumbnail URLs percent-encoded.
     */
    private static function encode(string $url): string
    {
        return str_replace('×', rawurlencode('×'), $url);
    }

    /**
     * @return string[]
     */
    private function thumbFolders(): array
    {
        $folders = [];
        foreach ((new Finder())->directories()->in(self::PUBLIC_DIR . '/thumbs')->depth(0) as $folder) {
            $folders[] = $folder->getFilename();
        }
        sort($folders);

        return $folders;
    }
}
