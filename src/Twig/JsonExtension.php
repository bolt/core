<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Common\Json;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

class JsonExtension extends AbstractExtension
{
    private const SERIALIZE_GROUP = 'get_content';

    private const SERIALIZE_GROUP_DEFINITION = 'get_definition';

    /** @var bool */
    private $includeDefinition = true;

    /** @var NormalizerInterface */
    private $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('normalize_records', [$this, 'normalizeRecords']),
            new TwigFilter('json_records', [$this, 'jsonRecords']),
            new TwigFilter('json_decode', [$this, 'jsonDecode']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [
            new TwigTest('json', [$this, 'testJson']),
        ];
    }

    public function jsonRecords($records, ?bool $includeDefinition = true, int $options = 0, string $locale = ''): string
    {
        $this->includeDefinition = $includeDefinition;

        return Json::json_encode($this->normalizeRecords($records, $locale), $options);
    }

    /**
     * @param Content|array|\Traversable $records
     */
    public function normalizeRecords($records, string $locale = ''): array
    {
        if ($records instanceof Content) {
            return $this->contentToArray($records, $locale);
        }

        if (is_array($records)) {
            $normalizedRecords = $records;
        } else {
            $normalizedRecords = iterator_to_array($records);
        }

        return array_map(function ($record) use ($locale) {
            return $this->contentToArray($record, $locale);
        }, $normalizedRecords);
    }

    private function contentToArray(Content $content, string $locale = ''): array
    {
        $group = [self::SERIALIZE_GROUP];

        if ($this->includeDefinition) {
            $group[] = self::SERIALIZE_GROUP_DEFINITION;
        }

        if (! empty($locale)) {
            // Set all translatable fields to the requested locale
            array_map(function (Field $field) use ($locale): void {
                if ($field->isTranslatable()) {
                    $field->setLocale($locale);
                }
            }, $content->getRawFields()->toArray());
        }

        // Get extras with the correct locales before normalizer overrides them
        // todo: Fix this :-)
        $extras = $content->getExtras();

        $result = $this->normalizer->normalize($content, null, [
            'groups' => $group,
        ]);

        $result['extras'] = $extras;

        return $result;
    }

    /**
     * Test whether a passed string contains valid JSON.
     */
    public function testJson(string $string): bool
    {
        return Json::test($string);
    }

    public function jsonDecode(string $json, $assoc = false, $depth = 512, $options = 0)
    {
        return json::json_decode($json, $assoc, $depth, $options);
    }
}
