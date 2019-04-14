<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class JsonExtension extends AbstractExtension
{
    private const SERIALIZE_GROUP = 'get_content';

    /**
     * @var NormalizerInterface
     */
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
        ];
    }

    public function jsonRecords($records): string
    {
        return \GuzzleHttp\json_encode($this->normalizeRecords($records));
    }

    public function normalizeRecords($records): array
    {
        if ($records instanceof Content) {
            return $this->contentToArray($records);
        }

        if (is_array($records)) {
            $normalizedRecords = $records;
        } elseif (is_iterable($records)) {
            $normalizedRecords = iterator_to_array($records);
        } else {
            throw new \InvalidArgumentException();
        }

        return array_map([$this, 'contentToArray'], $normalizedRecords);
    }

    private function contentToArray(Content $content): array
    {
        // we do it that way because in current API Platform version a Resource can't implement \JsonSerializable
        return $this->normalizer->normalize($content, null, ['group' => [self::SERIALIZE_GROUP]]);
    }
}
