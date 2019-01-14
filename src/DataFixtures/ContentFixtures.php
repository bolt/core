<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Tightenco\Collect\Support\Collection;

class ContentFixtures extends Fixture implements DependentFixtureInterface
{
    /** @var \Faker\Generator */
    private $faker;

    /** @var Collection */
    private $config;

    private $lastTitle = null;

    public function __construct(Config $config)
    {
        $this->faker = Factory::create();
        $this->config = $config->get('contenttypes');
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TaxonomyFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadContent($manager);

        $manager->flush();
    }

    private function loadContent(ObjectManager $manager): void
    {
        foreach ($this->config as $contentType) {
            $amount = $contentType['singleton'] ? 1 : 15;

            foreach (range(1, $amount) as $i) {
                $ref = $i === 0 ? 'admin' : ['admin', 'henkie', 'jane_admin', 'tom_admin'][random_int(0, 3)];
                /** @var User $author */
                $author = $this->getReference($ref);

                $content = new Content();
                $content->setContentType($contentType['slug']);
                $content->setAuthor($author);
                $content->setStatus($this->getRandomStatus());
                $content->setCreatedAt($this->faker->dateTimeBetween('-1 year'));
                $content->setModifiedAt($this->faker->dateTimeBetween('-1 year'));
                $content->setPublishedAt($this->faker->dateTimeBetween('-1 year'));
                $content->setDepublishedAt($this->faker->dateTimeBetween('-1 year'));

                $sortorder = 1;
                foreach ($contentType['fields'] as $name => $fieldType) {
                    if ($fieldType['localize']) {
                        $locales = $contentType['locales'];
                    } else {
                        $locales = [''];
                    }

                    foreach ($locales as $locale) {
                        $field = Field::factory($fieldType, $name);
                        $field->setName($name);
                        $field->setValue($this->getValuesforFieldType($name, $fieldType));
                        $field->setSortorder($sortorder++ * 5);
                        $field->setLocale($locale);

                        $content->addField($field);
                    }
                }

                $manager->persist($content);
            }
        }
    }

    private function getRandomStatus()
    {
        $statuses = ['published', 'published', 'published', 'held', 'draft', 'timed'];

        return $statuses[array_rand($statuses)];
    }

    private function getValuesforFieldType($name, $field)
    {
        switch ($field['type']) {
            case 'html':
            case 'textarea':
            case 'markdown':
                $data = [$this->faker->paragraphs(3, true)];
                break;
            case 'image':
                $data = [
                    'filename' => 'kitten.jpg',
                    'alt' => 'A cute kitten',
                ];
                break;
            case 'slug':
                $data = $this->lastTitle ?? [$this->faker->sentence(3, true)];
                break;
            case 'text':
                $data = [$this->faker->sentence(6, true)];
                break;
            default:
                $data = [$this->faker->sentence(6, true)];
        }

        if ($name === 'title') {
            $this->lastTitle = $data;
        }

        return $data;
    }
}
