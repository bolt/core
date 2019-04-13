<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Configuration\Areas;
use Bolt\Content\MediaFactory;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class ImagesFixtures extends BaseFixture
{
    /** @var Generator */
    private $faker;

    /** @var Collection */
    private $urls;

    /** @var MediaFactory */
    private $mediaFactory;

    /** @var Areas */
    private $areas;

    private const AMOUNT = 10;

    public function __construct(Areas $areas, MediaFactory $mediaFactory)
    {
        $this->urls = new Collection([
            'https://source.unsplash.com/1280x1024/?business,workspace,interior/',
            'https://source.unsplash.com/1280x1024/?cityscape,landscape,nature/',
            'https://source.unsplash.com/1280x1024/?animal,kitten,puppy,cute/',
            'https://source.unsplash.com/1280x1024/?technology/',
        ]);

        $this->faker = Factory::create();
        $this->mediaFactory = $mediaFactory;
        $this->areas = $areas;
    }

    public function load(ObjectManager $manager): void
    {
        $this->fetchImages();
        $this->loadImages($manager);

        $manager->flush();
    }

    private function fetchImages(): void
    {
        $outputPath = $this->areas->get('files', 'basepath') . '/stock/';

        if (! is_dir($outputPath)) {
            mkdir($outputPath);
        }

        for ($i = 1; $i <= self::AMOUNT; $i++) {
            $url = $this->urls->random() . random_int(10000, 99999);
            $filename = 'image_' . random_int(10000, 99999) . '.jpg';

            $client = new Client();
            $resource = fopen($outputPath . $filename, 'w');
            $client->request('GET', $url, ['sink' => $resource]);
        }
    }

    private function loadImages(ObjectManager $manager): void
    {
        $path = $this->areas->get('files', 'basepath') . '/stock/';

        $index = $this->getImagesIndex($path);

        foreach ($index as $file) {
            $media = $this->mediaFactory->createOrUpdateMedia($file, 'files', $this->faker->sentence(6, true));
            $media->setAuthor($this->getRandomReference('user'))
                ->setDescription($this->faker->paragraphs(3, true))
                ->setCopyright('© Unsplash');

            $manager->persist($media);
        }

        $manager->flush();
    }
}
