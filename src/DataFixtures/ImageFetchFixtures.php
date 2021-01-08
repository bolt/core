<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Configuration\Config;
use Bolt\Configuration\FileLocations;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use PhpZip\ZipFile;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpClient\HttpClient;

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

        $client = HttpClient::create();

        $progress = new ProgressBar($output, 100);
        $progress->setRedrawFrequency(5);
        $progress->start();

        $this->curlOptions['on_progress'] = function (int $downloaded) use ($progress): void {
            if ($downloaded > 0) {
                // The file is about 9 mb, we count to 80%, so 9000000 / 80 = 112500
                $progress->setProgress((int) round($downloaded / 112500));
            }
        };

        $file = $client->request('GET', self::URL, $this->curlOptions)->getContent();

        fwrite($resource, $file);
        fclose($resource);

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
