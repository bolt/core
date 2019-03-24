<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Collection\DeepCollection;
use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\User;
use Bolt\Enum\Statuses;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\Entity\Translation;
use Tightenco\Collect\Support\Collection;

class ContentFixtures extends Fixture implements DependentFixtureInterface
{
    /** @var Generator */
    private $faker;

    /** @var Collection */
    private $config;

    private $lastTitle = null;

    /** @var array */
    private $presetRecords = [];

    public function __construct(Config $config)
    {
        $this->faker = Factory::create();
        $this->presetRecords = $this->getPresetRecords();
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
        /** @var TranslationRepository $translationRepository */
        $translationRepository = $manager->getRepository(Translation::class);

        foreach ($this->config as $contentType) {
            $amount = $contentType['singleton'] ? 1 : (int) ($contentType['listing_records'] * 3);

            foreach (range(1, $amount) as $i) {
                $ref = $i === 1 ? 'admin' : ['admin', 'henkie', 'jane_admin', 'tom_admin'][random_int(0, 3)];
                /** @var User $author */
                $author = $this->getReference($ref);

                $content = new Content();
                $content->setContentType($contentType['slug']);
                $content->setAuthor($author);
                $content->setCreatedAt($this->faker->dateTimeBetween('-1 year'));
                $content->setModifiedAt($this->faker->dateTimeBetween('-1 year'));
                $content->setPublishedAt($this->faker->dateTimeBetween('-1 year'));
                $content->setDepublishedAt($this->faker->dateTimeBetween('-1 year'));

                $preset = $this->getPreset($contentType['slug']);

                if ($i === 1 || ! empty($preset)) {
                    $content->setStatus(Statuses::PUBLISHED);
                } else {
                    $content->setStatus($this->getRandomStatus());
                }

                $sortorder = 1;
                foreach ($contentType['fields'] as $name => $fieldType) {
                    $field = Field::factory($fieldType, $name);
                    $field->setName($name);

                    if (isset($preset[$name])) {
                        $field->setValue($preset[$name]);
                    } else {
                        $field->setValue($this->getValuesforFieldType($name, $fieldType));
                    }
                    $field->setSortorder($sortorder++ * 5);

                    $content->addField($field);

                    if ($fieldType['localize']) {
                        foreach ($contentType['locales'] as $locale) {
                            $translationRepository->translate($field, 'value', $locale, $field->getValue());
                        }
                    }
                }

                $manager->persist($content);
            }
        }
    }

    private function getRandomStatus(): string
    {
        $statuses = ['published', 'published', 'published', 'held', 'draft', 'timed'];

        return $statuses[array_rand($statuses)];
    }

    private function getValuesforFieldType(string $name, DeepCollection $field): array
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

        if ($name === 'title' || $name === 'heading') {
            $this->lastTitle = $data;
        }

        return $data;
    }

    private function getPresetRecords(): array
    {
        $records['entries'][] = [
            'title' => 'This is a record in the "Entries" ContentType',
            'slug' => 'This is a record in the "Entries" ContentType',
        ];
        $records['blocks'][] = [
            'title' => 'About',
        ];
        $records['blocks'][] = [
            'title' => 'Search',
        ];

        $records['tests'][] = [
            'selectfield' => 'bar',
            'multiselect' => 'Michelangelo',
            'slug' => 'title-of-the-test',
            'title' => 'Title of the test',
            'text_markup' => 'Text with <em>markup allowed</em>.',
            'text_plain' => 'Text with <strong>no</strong> markup allowed.',
            'textarea_field' => 'Textarea field with <em>simple</em> HTML in it.',
            'html_field' => 'HTML field with <em>simple</em> HTML in it.',
            'markdown_field' => 'Markdown field  with *simple* Markdown in it.',
        ];

        return $records;
    }

    private function getPreset(string $slug): array
    {
        if (isset($this->presetRecords[$slug]) && ! empty($this->presetRecords[$slug])) {
            $preset = array_pop($this->presetRecords[$slug]);
        } else {
            $preset = [];
        }

        return $preset;
    }
}
