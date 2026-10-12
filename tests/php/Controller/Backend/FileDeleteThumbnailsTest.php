<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller\Backend;

use Bolt\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The web server serves saved thumbnails straight from disk, so deleting an image (or its folder)
 * in the file manager must remove its thumbnails as well, or they stay public.
 */
class FileDeleteThumbnailsTest extends WebTestCase
{
    private const PUBLIC_DIR = __DIR__ . '/../../../../public';

    private const FOLDER = 'file-delete-thumbnails-test';

    private const THUMBNAIL_URL = '/thumbs/9124×94/' . self::FOLDER . '/kitten.jpg';

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        (new Filesystem())->copy(self::PUBLIC_DIR . '/files/kitten.jpg', self::PUBLIC_DIR . '/files/' . self::FOLDER . '/kitten.jpg');

        $this->client = static::createClient();
        $this->client->request('GET', str_replace('×', rawurlencode('×'), self::THUMBNAIL_URL));
        $this->assertFileExists(self::PUBLIC_DIR . self::THUMBNAIL_URL);

        $this->client->loginUser(static::getContainer()->get(UserRepository::class)->findOneByUsername('admin'));
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove([
            self::PUBLIC_DIR . '/files/' . self::FOLDER,
            self::PUBLIC_DIR . '/thumbs/9124×94',
        ]);

        parent::tearDown();
    }

    public function testDeletingAFileRemovesItsThumbnails(): void
    {
        $crawler = $this->client->request('GET', '/bolt/filemanager/files?path=/' . self::FOLDER . '/');
        $this->client->click($crawler->filter('a.delete[href*="file-delete"]')->link());

        $this->assertFileDoesNotExist(self::PUBLIC_DIR . '/files/' . self::FOLDER . '/kitten.jpg');
        $this->assertFileDoesNotExist(self::PUBLIC_DIR . self::THUMBNAIL_URL);
    }

    public function testDeletingAFolderRemovesTheThumbnailsInIt(): void
    {
        $crawler = $this->client->request('GET', '/bolt/filemanager/files?path=/');
        $this->client->click($crawler->filter(sprintf('a[href*="filemanager-actions/delete"][href*="%s"]', self::FOLDER))->link());

        $this->assertDirectoryDoesNotExist(self::PUBLIC_DIR . '/files/' . self::FOLDER);
        $this->assertFileDoesNotExist(self::PUBLIC_DIR . self::THUMBNAIL_URL);
    }
}
