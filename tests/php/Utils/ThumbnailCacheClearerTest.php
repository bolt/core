<?php

declare(strict_types=1);

namespace Bolt\Tests\Utils;

use Bolt\Configuration\Config;
use Bolt\Utils\ThumbnailCacheClearer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

/**
 * The web server serves saved thumbnails straight from disk, so the thumbnails of a deleted image
 * must be removed with it, or they stay public.
 */
class ThumbnailCacheClearerTest extends KernelTestCase
{
    private const FOLDER = 'thumbnail-cache-clearer-test';

    private Config $config;

    private Filesystem $filesystem;

    /** @var string[] */
    private array $created = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = static::getContainer()->get(Config::class);
        $this->filesystem = new Filesystem();
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->created);

        parent::tearDown();
    }

    public function testThumbnailsOfAFileAreRemoved(): void
    {
        $image = $this->files(self::FOLDER . '/kitten.jpg');
        $thumbnails = [
            $this->thumbs('9123×94/' . self::FOLDER . '/kitten.jpg'),
            $this->thumbs('9123×94×80×crop×files/' . self::FOLDER . '/kitten.jpg'),
            // Thumbnails with the web root as location have the path relative to the web root.
            $this->thumbs('9123×94×80×crop×web/files/' . self::FOLDER . '/kitten.jpg'),
            // Saved by Bolt before thumbnails were saved at the path of their URL.
            $this->thumbs('9123_94_80_crop_files/' . self::FOLDER . '/kitten.jpg'),
        ];
        $glideCache = $this->create($this->config->getPath('cache', true, ['thumbnails', 'files', self::FOLDER, 'kitten.jpg', 'abc123']));

        $other = [
            $this->files(self::FOLDER . '/kitten2.jpg'),
            $this->thumbs('9123×94/' . self::FOLDER . '/kitten2.jpg'),
            $this->thumbs('9123×94/' . self::FOLDER . '/kitten.jpg.webp'),
        ];

        $this->filesystem->remove($image);

        $this->assertTrue(static::getContainer()->get(ThumbnailCacheClearer::class)->clearFor($image));

        foreach ([...$thumbnails, $glideCache] as $removed) {
            $this->assertFileDoesNotExist($removed);
        }
        foreach ($other as $kept) {
            $this->assertFileExists($kept);
        }
    }

    public function testThumbnailsOfAFolderAreRemoved(): void
    {
        $folder = $this->config->getPath('files', true, self::FOLDER);
        $this->files(self::FOLDER . '/sub/kitten.jpg');
        $thumbnails = [
            $this->thumbs('9123×94/' . self::FOLDER . '/sub/kitten.jpg'),
            $this->thumbs('9123×94/' . self::FOLDER . '/kitten.jpg'),
        ];
        $other = $this->thumbs('9123×94/' . self::FOLDER . '-other/kitten.jpg');

        $this->filesystem->remove($folder);

        $this->assertTrue(static::getContainer()->get(ThumbnailCacheClearer::class)->clearFor($folder));

        foreach ($thumbnails as $removed) {
            $this->assertFileDoesNotExist($removed);
        }
        $this->assertFileExists($other);
    }

    public function testNothingIsRemovedForAPathOutsideTheWebRoot(): void
    {
        $thumbnail = $this->thumbs('9123×94/' . self::FOLDER . '/kitten.jpg');

        $this->assertTrue(static::getContainer()->get(ThumbnailCacheClearer::class)->clearFor($this->config->getPath('site', true, self::FOLDER)));

        $this->assertFileExists($thumbnail);
    }

    private function files(string $path): string
    {
        return $this->create($this->config->getPath('files', true, $path));
    }

    private function thumbs(string $path): string
    {
        return $this->create($this->config->getPath('thumbs', true, $path));
    }

    private function create(string $path): string
    {
        $this->filesystem->dumpFile($path, 'image');

        // Remove the topmost folder that was created for the file.
        foreach ($this->roots() as $root) {
            if (Path::isBasePath($root, $path)) {
                $this->created[] = Path::join($root, explode('/', Path::makeRelative($path, $root))[0]);
            }
        }

        return $path;
    }

    /**
     * @return string[]
     */
    private function roots(): array
    {
        return [
            $this->config->getPath('files'),
            $this->config->getPath('thumbs'),
            $this->config->getPath('cache', true, ['thumbnails', 'files']),
        ];
    }
}
