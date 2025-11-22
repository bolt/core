<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Configuration\FileLocations;
use Bolt\Entity\User;
use Bolt\Factory\MediaFactory;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class ImagesFixtures extends BaseFixture implements FixtureGroupInterface
{
    private readonly Generator $faker;

    public function __construct(
        private readonly FileLocations $fileLocations,
        private readonly MediaFactory $mediaFactory
    ) {
        $this->faker = Factory::create();
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
            $media->setAuthor($this->getRandomReference(User::class))
                ->setDescription($this->faker->paragraphs(3, true))
                ->setCopyright('© Unsplash');

            $manager->persist($media);
        }

        $manager->flush();
    }
}
