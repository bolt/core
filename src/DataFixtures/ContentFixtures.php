<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Collection\DeepCollection;
use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Configuration\FileLocations;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\Field\SelectField;
use Bolt\Enum\Statuses;
use Bolt\Repository\FieldRepository;
use Bolt\Twig\ContentExtension;
use Bolt\Utils\FakeContent;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tightenco\Collect\Support\Collection;

class ContentFixtures extends BaseFixture implements DependentFixtureInterface, FixtureGroupInterface
{
    /** @var Generator */
    private $faker;

    /** @var array */
    private $presetRecords = [];

    /** @var Collection */
    private $imagesIndex;

    /** @var Config */
    private $config;

    /** @var FileLocations */
    private $fileLocations;

    /** @var string */
    private $defaultLocale;

    /** @var TagAwareCacheInterface */
    private $cache;

    /** @var ContentExtension */
    private $contentExtension;

    public function __construct(Config $config, FileLocations $fileLocations, TagAwareCacheInterface $cache, string $defaultLocale, ContentExtension $contentExtension)
    {
        $this->config = $config;
        $this->faker = Factory::create();
        $seed = $this->config->get('general/fixtures_seed');
        if (! empty($seed)) {
            $this->faker->seed($seed);
        }

        $this->presetRecords = $this->getPresetRecords();
        $this->fileLocations = $fileLocations;
        $this->defaultLocale = $defaultLocale;
        $this->cache = $cache;
        $this->contentExtension = $contentExtension;
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TaxonomyFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['with-images', 'without-images'];
    }

    public function load(ObjectManager $manager): void
    {
        $path = $this->fileLocations->get('files')->getBasepath();
        $this->imagesIndex = $this->getImagesIndex($path);

        $this->loadContent($manager);

        $manager->flush();

        $this->cache->invalidateTags([
            'backendmenu',
            'frontendmenu'
        ]);

        $this->setSelectFieldsMappedWithContent($manager);
    }

    private function loadContent(ObjectManager $manager): void
    {
        foreach ($this->config->get('contenttypes') as $contentType) {
            // Only add Singletons on first run, not when appending
            if ($this->getOption('--append') && $contentType['singleton']) {
                continue;
            }

            $amount = $contentType['singleton'] ? 1 : (int) ($contentType['listing_records'] * 3);

            for ($i = 1; $i <= $amount; $i++) {
                if ($i === 1) {
                    $author = $this->getReference('user_admin');
                } else {
                    $author = $this->getRandomReference('user');
                }

                $content = new Content();
                $content->setDefinition($contentType);
                $content->setContentType($contentType['slug']);
                $content->setAuthor($author);
                $content->setCreatedAt($this->faker->dateTimeBetween('-1 year'));
                $content->setModifiedAt($this->faker->dateTimeBetween('-1 year'));
                $content->setPublishedAt($this->faker->dateTimeBetween('-1 year'));

                $content->setContentExtension($this->contentExtension);

                $preset = $this->getPreset($contentType['slug']);

                if ($i === 1 || ! empty($preset)) {
                    $content->setStatus($preset['status'] ?? Statuses::PUBLISHED);
                } else {
                    $content->setStatus($this->getRandomStatus());
                }

                $fields = collect($contentType['fields']);

                // Load all fields, except slugs.
                $fields->filter(function ($field) {
                    return $field['type'] !== 'slug';
                })->map(function ($fieldType, $name) use ($content, $contentType, $preset): void {
                    $this->loadField($content, $name, $fieldType, $contentType, $preset);
                });

                // Load slug fields, to make sure `uses` can be used.
                $fields->filter(function ($field) {
                    return $field['type'] === 'slug';
                })->map(function ($fieldType, $name) use ($content, $contentType, $preset): void {
                    $this->loadField($content, $name, $fieldType, $contentType, $preset);
                });

                foreach ($contentType['taxonomy'] as $taxonomySlug) {
                    if ($taxonomySlug === 'categories') {
                        if (isset($preset['taxonomy:categories'])) {
                            // preset categories
                            foreach ($preset['taxonomy:categories'] as $taxonomyCategoryLabel) {
                                $taxonomy = $this->getReference('taxonomy_categories_' . $taxonomyCategoryLabel);
                                $content->addTaxonomy($taxonomy);
                            }
                            // add no additional random categories
                            $taxonomyAmount = 0;
                        } else {
                            $taxonomyAmount = 2;
                        }
                    } elseif ($taxonomySlug === 'tags') {
                        $taxonomyAmount = 4;
                    } else {
                        $taxonomyAmount = 1;
                    }

                    if ($taxonomyAmount > 0) {
                        foreach ($this->getRandomTaxonomies($taxonomySlug, $taxonomyAmount) as $taxonomy) {
                            $content->addTaxonomy($taxonomy);
                        }
                    }
                }

                $refKey = sprintf('content_%s_%s', $contentType['slug'], $content->getSlug());
                $this->setReference($refKey, $content);

                $manager->persist($content);
            }
        }
    }

    private function loadCollectionField(Content $content, Field $field, $fieldType, ContentType $contentType, array $preset): Field
    {
        $collectionItems = $field->getDefinition()->get('fields');

        $i = 0;
        foreach ($collectionItems as $name => $type) {
            $child = $this->loadField($content, $name, $type, $contentType, $preset, false);
            $child->setParent($field);
            $child->setSortorder($i);
            $content->addField($child);
            ++$i;
        }

        return $field;
    }

    private function loadSetField(Content $content, Field $set, ContentType $contentType, array $preset): Field
    {
        $setChildren = $set->getDefinition()->get('fields');

        $children = [];
        foreach ($setChildren as $setChild => $setChildType) {
            $child = $this->loadField($content, $setChild, $setChildType, $contentType, $preset, false);
            $children[] = $child;
        }

        $set->setValue($children);

        return $set;
    }

    private function loadField(Content $content, string $name, $fieldType, ContentType $contentType, array $preset, bool $addToContent = true): Field
    {
        $sortorder = 1;

        $field = FieldRepository::factory($fieldType, $name);

        // Make sure to create the fixture in the correct default locale.
        $field->setLocale($this->defaultLocale);

        if (isset($preset[$name])) {
            $field->setValue($preset[$name]);
        } else {
            if ($fieldType['type'] === 'collection') {
                $field = $this->loadCollectionField($content, $field, $fieldType, $contentType, $preset);
            } elseif ($fieldType['type'] === 'set') {
                $field = $this->loadSetField($content, $field, $contentType, $preset);
            } else {
                $field->setValue($this->getValuesforFieldType($fieldType, $contentType['singleton'], $content));
            }
        }
        $field->setSortorder($sortorder++ * 5);

        if ($addToContent) {
            $content->addField($field);
        }

        // Prepopulate locales. Leave last one empty for tests.
        if (isset($fieldType['localize']) && $fieldType['localize']) {
            $locales = $contentType['locales']->toArray();
            foreach ($locales as $locale) {
                if ($locale !== $this->defaultLocale && array_search($locale, $locales, true) !== count($locales) - 1) {
                    $value = $preset[$name] ?? $this->getValuesforFieldType($fieldType, $contentType['singleton'], $content);
                    $field->translate($locale, false)->setValue($value);
                }
            }

            $field->mergeNewTranslations();
        }

        return $field;
    }

    private function getRandomStatus(): string
    {
        $statuses = ['published', 'published', 'published', 'held', 'draft', 'timed'];

        return $statuses[array_rand($statuses)];
    }

    private function getValuesforFieldType(DeepCollection $field, bool $singleton, Content $content): array
    {
        return
            isset($field['fixture_format']) ?
                $this->getFixtureFormatValues($field['fixture_format'])
                :
                $this->getFieldTypeValue($field, $singleton, $content);
    }

    private function getFixtureFormatValues(string $format): array
    {
        return [
            preg_replace_callback(
                        '/{([\w]+)}/i',
                        function ($match) {
                            $match = $match[1];

                            try {
                                return $this->faker->{$match};
                            } finally {
                            }

                            return '(unknown)';
                        },
                        $format
                    ),
        ];
    }

    private function getFieldTypeValue(DeepCollection $field, bool $singleton, Content $content)
    {
        switch ($field['type']) {
            case 'html':
            case 'redactor':
                $data = [FakeContent::generateHTML(4)];

                break;
            case 'article':
                $data = [FakeContent::generateHTML(12)];

                break;
            case 'markdown':
                $data = [FakeContent::generateMarkdown(4)];

                break;
            case 'textarea':
                $data = [$this->faker->paragraphs(3, true)];

                break;
            case 'image':
                $randomImage = $this->imagesIndex->random();
                $data = [
                    'filename' => str_replace('\\', '/', $randomImage->getRelativePathname()),
                    'alt' => $this->faker->sentence(4, true),
                    'media' => '',
                ];

                break;
            case 'file':
                $randomImage = $this->imagesIndex->random();
                $data = [
                    'filename' => str_replace('\\', '/', $randomImage->getRelativePathname()),
                    'title' => $this->faker->sentence(4, true),
                    'media' => '',
                ];

                break;
            case 'slug':
                if (isset($field['uses'])) {
                    $fields = collect($field['uses']);
                    $data = $fields->reduce(function (string $carry, string $current) use ($content) {
                        $value = $content->hasField($current) ? $content->getFieldValue($current) : '';

                        return $carry . $value;
                    }, '');
                } else {
                    $data = $this->faker->sentence(3, true);
                }

                $data = [$data];

                break;
            case 'text':
                $words = isset($field['slug']) && in_array($field['slug'], ['title', 'heading'], true) ? 3 : 7;
                $data = [$this->faker->sentence($words, true)];

                break;
            case 'email':
                $data = [$this->faker->email];

                break;
            case 'templateselect':
                $data = [];

                break;
            case 'date':
            case 'datetime':
                $data = [$this->faker->dateTime()->format('c')];

                break;
            case 'number':
                $data = [(string) $this->faker->numberBetween(-100, 1000)];

                break;
            case 'checkbox':
                $data = [$this->faker->boolean()];

                break;
            case 'data':
                $data = [];
                for ($i = 1; $i < 5; $i++) {
                    $data[$this->faker->sentence(1)] = $this->faker->sentence(4, true);
                }

                break;
            case 'imagelist':
                $data = [];
                for ($i = 1; $i < 5; $i++) {
                    $randomImage = $this->imagesIndex->random();
                    $data[] = [
                        'filename' => $randomImage->getRelativePathname(),
                        'alt' => $this->faker->sentence(4, true),
                        'media' => '',
                    ];
                }

                break;
            case 'filelist':
                $data = [];
                for ($i = 1; $i < 5; $i++) {
                    $randomImage = $this->imagesIndex->random();
                    $data[] = [
                        'filename' => $randomImage->getRelativePathname(),
                        'title' => $this->faker->sentence(4, true),
                        'media' => '',
                    ];
                }

                break;
            case 'select':
                $data = [];

                if (isset($field['values']) && ($field['values'] instanceof ContentType)) {
                    $data = [\array_rand($field['values']->all())];
                }

                break;
            case 'geolocation':
                $data = ['selected' => 'latlong', 'zoom' => '7', 'search' => ''];
                $coordinates = $this->faker->localCoordinates();
                $data['lat'] = $coordinates['latitude'];
                $data['long'] =$coordinates['longitude'];
                $data = [json_encode($data)];


                break;
            case 'seo':
                $data = ['keywords' => '', 'shortlink' => '', 'canonical' => '', 'robots' => '', 'og' => ''];
                $data['title'] = $this->faker->sentence(4, true);
                $data['description'] = $this->faker->sentence(120, true);
                $data = [json_encode($data)];

                break;
            default:
                $data = [$this->faker->sentence(6, true)];
        }

        return $data;
    }

    private function getPresetRecords(): array
    {
        $records['entries'][] = [
            'title' => 'This is a record in the "Entries" ContentType',
            'slug' => 'This is a record in the "Entries" ContentType',
            'taxonomy:categories' => ['love', 'books'],
        ];
        $records['blocks'][] = [
            'title' => 'About This Site',
            'slug' => 'about',
        ];
        $records['blocks'][] = [
            'title' => 'Search',
            'slug' => 'search',
        ];
        $records['blocks'][] = [
            'title' => 'Our People',
            'slug' => 'people',
        ];
        $records['blocks'][] = [
            'title' => 'Call to Action',
            'slug' => 'call-to-action',
        ];
        $records['blocks'][] = [
            'title' => 'Welcome to your new site',
            'slug' => 'hero-section',
            'teaser' => 'Take a look around, this is Bolt\'s default 2021 theme. To edit your site\'s content,'
                . ' go <a href="/bolt/">login with</a> your username and password.'
                . ' Documentation, examples and support are available on the official <a href="https://boltcms.io">Bolt website</a>.'
                . ' Happy building 👩🏾‍💻 🧑‍🎨',
        ];
        $records['blocks'][] = [
            'title' => 'Introduction',
            'slug' => 'introduction',
        ];
        $records['blocks'][] = [
            'title' => 'Products',
            'slug' => 'products',
        ];
        $records['blocks'][] = [
            'title' => '404 Page not found',
            'slug' => '404-not-found',
            'status' => Statuses::HELD,
        ];
        $records['blocks'][] = [
            'title' => '403 Forbidden',
            'slug' => '403-forbidden',
            'status' => Statuses::HELD,
        ];
        $records['blocks'][] = [
            'title' => '503 Service Unavailable (Maintenance Mode)',
            'slug' => '503-maintenance mode',
            'status' => Statuses::HELD,
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
            'markdown_field' => 'Markdown field with *simple* Markdown in it.',
            'text_not_sanitised' => 'Text field with <strong>markup</strong>, including <script>console.log(\'hoi\')</script>. The end.',
            'text_sanitised' => 'Text field with <strong>markup</strong>, including <script>console.log(\'hoi\')</script>. The end.',
            'attachment' => [
                'filename' => 'joey.jpg',
                'title' => $this->faker->sentence(4, true),
                'media' => '',
            ],
        ];
        $records['pages'][] = [
            'heading' => 'This is a page',
            'slug' => 'this-is-a-page',
        ];

        // Only add this fixture if the file exists: It does in the "Git Clone", but not in the
        // "Composer create-project".
        $file = dirname(dirname(__DIR__)) . '/public/theme/skeleton/custom/setcontent_1.twig';
        if (file_exists($file)) {
            $records['pages'][] = [
                'heading' => 'Setcontent test page',
                'slug' => 'Setcontent test page',
                'template' => 'custom/setcontent_1.twig',
            ];
        }

        // Only add this fixture if the file exists: It does in the "Git Clone", but not in the
        // "Composer create-project".
        $file = dirname(dirname(__DIR__)) . '/public/theme/skeleton/custom/setwherecheckbox_1.twig';
        if (file_exists($file)) {
            $records['pages'][] = [
                'heading' => 'SetContent Where Checkbox test page',
                'slug' => 'Setwherecheckbox test page',
                'template' => 'custom/setwherecheckbox_1.twig',
            ];
        }

        return $records;
    }

    private function getPreset(string $slug): array
    {
        if (isset($this->presetRecords[$slug]) && ! empty($this->presetRecords[$slug]) && ! $this->getOption('--append')) {
            $preset = array_shift($this->presetRecords[$slug]);
        } else {
            $preset = [];
        }

        return $preset;
    }

    private function setSelectFieldsMappedWithContent(ObjectManager $manager): void
    {
        /** @var Content[] $allContent */
        $allContent = $manager->getRepository(Content::class)->findAll();

        foreach ($allContent as $content) {
            $contentDefaultLocale = $content->getDefaultLocale();
            $contentLocales = $content->getLocales();
            foreach ($content->getFields() as $field) {
                if (! $this->isSelectFieldAndMappedWithContent($field)) {
                    continue;
                }

                /** @var SelectField $field */
                $contentType = $field->getContentType();

                if (! \is_string($contentType)) {
                    continue;
                }

                try {
                    /** @var Content $randomReference */
                    $randomReference = $this->getRandomReference(\sprintf('content_%s', $contentType));
                } catch (\Exception $exception) {
                    continue;
                }

                $content->setFieldValue($field->getName(), [$randomReference->getId()], $this->defaultLocale);

                foreach ($contentLocales as $locale) {
                    if (
                        $locale !== $contentDefaultLocale
                        && array_search($locale, $contentLocales->toArray(), true) !== (count($contentLocales) - 1)
                    ) {
                        $content->setFieldValue($field->getName(), [$randomReference->getId()], $locale);
                    }
                }
            }

            $manager->persist($content);
        }

        $manager->flush();
    }

    private function isSelectFieldAndMappedWithContent(Field $field): bool
    {
        if (! $field instanceof SelectField) {
            return FALSE;
        }

        if (! $field->isContentSelect()) {
            return FALSE;
        }

        return TRUE;
    }
}
