<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Configuration\Config;
use Bolt\Configuration\FileLocations;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemOperator;
use PhpZip\ZipFile;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpClient\HttpClient;

class ImageFetchFixtures extends BaseFixture implements FixtureGroupInterface
{
    private const URL = 'https://placeholder.boltcms.io/getfiles';

    /** @var FileLocations */
    private $fileLocations;

    /** @var FilesystemOperator */
    private $filesStorage;

    private const MAX_AMOUNT = 50;

    /** @var array */
    private $curlOptions;

    public function __construct(FileLocations $fileLocations, Config $config, FilesystemOperator $filesStorage)
    {
        $this->curlOptions = $config->get('general/curl_options')->all();

        $this->fileLocations = $fileLocations;

        $this->filesStorage = $filesStorage;
    }

    public static function getGroups(): array
    {
        return ['with-images'];
    }

    public function load(ObjectManager $manager): void
    {
        // We only fetch more images, if we're currently under the MAX_AMOUNT
        if (count($this->filesStorage->listContents('/stock')->toArray()) <= self::MAX_AMOUNT) {
            $this->fetchImages();
        }
    }

    private function fetchImages(): void
    {
        $output = new ConsoleOutput();

        $progress = new ProgressBar($output, 100);
        $progress->setRedrawFrequency(5);

        $this->curlOptions['on_progress'] = function (int $downloaded) use ($progress): void {
            if ($downloaded > 0) {
                // The file is about 9 mb, we count to 80%, so 9000000 / 80 = 112500
                $progress->setProgress((int) round($downloaded / 112500));
            }
        };

        $client = HttpClient::create();
        $file = $client->request('GET', self::URL, $this->curlOptions)->getContent();

        $this->filesStorage->write($this->getOutputFile(), $file, ['visibility' => 'public']);

        $progress->finish();

        $zipFile = new ZipFile();

        $basePath = $this->fileLocations->get('files')->getBasepath();
        $zipFile->openFile($basePath . $this->getOutputFile())->extractTo($basePath . '/stock');

        $output->writeln('');
    }

    private function getOutputPath(): string
    {
        if (! $this->filesStorage->directoryExists('/stock')) {
            $this->filesStorage->createDirectory('/stock');
        }

        return '/stock/';
    }

    private function getOutputFile(): string
    {
        return $this->getOutputPath() . 'placeholders.zip';
    }
}
