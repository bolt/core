<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Configuration\FileLocations;
use Bolt\Factory\MediaFactory;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class ImageFetchFixtures extends BaseFixture implements FixtureGroupInterface
{
    /** @var Generator */
    private $faker;

    /** @var Collection */
    private $urls;

    /** @var MediaFactory */
    private $mediaFactory;

    /** @var FileLocations */
    private $fileLocations;

    private const AMOUNT = 10;
    private const MAX_AMOUNT = 40;

    public function __construct(FileLocations $fileLocations, MediaFactory $mediaFactory)
    {
        $this->urls = new Collection([
            'https://source.unsplash.com/1280x1024/?business,workspace,interior/',
            'https://source.unsplash.com/1280x1024/?cityscape,landscape,nature/',
            'https://source.unsplash.com/1280x1024/?animal,kitten,puppy,cute/',
            'https://source.unsplash.com/1280x1024/?technology/',
        ]);

        $this->faker = Factory::create();
        $this->mediaFactory = $mediaFactory;
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
        $outputPath = $this->fileLocations->get('files')->getBasepath() . '/stock/';

        if (! is_dir($outputPath)) {
            mkdir($outputPath);
        }

        for ($i = 1; $i <= self::AMOUNT; $i++) {
            $url = $this->urls->random() . random_int(10000, 99999);
            $filename = 'image_' . random_int(10000, 99999) . '.jpg';

            $client = new Client();
            $resource = fopen($outputPath . $filename, 'w');
            $client->request('GET', $url, ['sink' => $resource]);
            echo ' image';
        }
    }
}
