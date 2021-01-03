<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Configuration\Config;
use Bolt\Configuration\FileLocations;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use PhpZip\ZipFile;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class ImageFetchFixtures extends BaseFixture implements FixtureGroupInterface
{
    private const URL = 'https://placeholder.boltcms.io/getfiles';

    /** @var FileLocations */
    private $fileLocations;

    private const MAX_AMOUNT = 50;

    /** @var array */
    private $curlOptions;

    public function __construct(FileLocations $fileLocations, Config $config)
    {
        $this->curlOptions = $config->get('general/curl_options')->all();

        $this->fileLocations = $fileLocations;
    }

    public static function getGroups(): array
    {
        return ['with-images'];
    }

    public function load(ObjectManager $manager): void
    {
        $path = $this->fileLocations->get('files')->getBasepath();

        // We only fetch more images, if we're currently under the MAX_AMOUNT
        if ($this->getImagesIndex($path)->count() <= self::MAX_AMOUNT) {
            $this->fetchImages();
        }
    }

    private function fetchImages(): void
    {
        $output = new ConsoleOutput();
        $resource = fopen($this->getOutputFile(), 'w');

        $client = new Client([
            'progress' => function ($total, $downloaded) use ($output, &$progress): void {
                if ($total > 0 && $progress === null) {
                    $progress = new ProgressBar($output, 100);
                    $progress->setRedrawFrequency(5);
                    $progress->start();
                }

                if ($downloaded > 0) {
                    $progress->setProgress((int) round($downloaded / $total * 80.0));
                }
            },
            'sink' => $resource,
        ]);

        $client->request('GET', self::URL, $this->curlOptions);
        $progress->finish();

        $zipFile = new ZipFile();

        $zipFile->openFile($this->getOutputFile())->extractTo($this->getOutputPath());

        $output->writeln('');
    }

    private function getOutputPath(): string
    {
        $outputPath = $this->fileLocations->get('files')->getBasepath() . '/stock/';

        if (! is_dir($outputPath)) {
            mkdir($outputPath);
        }

        return $outputPath;
    }

    private function getOutputFile(): string
    {
        return $this->getOutputPath() . 'placeholders.zip';
    }
}
