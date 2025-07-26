<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RelationsFixtures extends BaseFixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const AMOUNT = 15;

    public function __construct(
        private readonly Config $config
    ) {
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TaxonomyFixtures::class,
            ContentFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['with-images', 'without-images'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->flushReferencesIndex();

        $this->loadContent($manager);

        $manager->flush();
    }

    private function loadContent(ObjectManager $manager): void
    {
        $contentTypes = $this->config->get('contenttypes');

        foreach ($contentTypes as $contentType) {
            foreach ($contentType['relations']->keys() as $contentTypeTo) {
                for ($i = 1; $i <= self::AMOUNT; $i++) {
                    $this->addRelation($contentType['slug'], $contentTypeTo, $manager);
                }
            }
        }
    }

    private function addRelation(string $contentTypeFrom, string $contentTypeTo, ObjectManager $manager): void
    {
        /** @var Content $contentFrom */
        $contentFrom = $this->getRandomReference('content_' . $contentTypeFrom);

        /** @var Content $contentTo */
        $contentTo = $this->getRandomReference('content_' . $contentTypeTo);

        $relation = new Relation($contentFrom, $contentTo);

        $manager->persist($relation);
    }
}
