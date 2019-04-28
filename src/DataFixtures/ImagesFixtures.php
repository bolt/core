<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Configuration\FileLocations;
use Bolt\Factory\MediaFactory;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Collection;

class ImagesFixtures extends BaseFixture implements FixtureGroupInterface
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
        $this->faker = Factory::create();
        $this->mediaFactory = $mediaFactory;
        $this->fileLocations = $fileLocations;
    }

    public static function getGroups(): array
    {
        return ['with-images', 'without-images'];
    }

    public function load(ObjectManager $manager): void
    {
        // Regardless of whether we fetch images, we still populate the Media Entities
        $this->loadImages($manager);

        $manager->flush();
    }

    private function loadImages(ObjectManager $manager): void
    {
        $path = $this->fileLocations->get('files')->getBasepath();

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
