<?php

namespace Bolt\Cache;

use Bolt\Entity\Field;
use Bolt\Twig\FieldExtension;

class SelectOptionsCacher extends FieldExtension implements CachingInterface
{
    use CachingTrait;

    public const CACHE_CONFIG_KEY = 'selectoptions';

    public function selectOptionsHelper(string $contentTypeSlug, array $params, Field $field, string $format): array
    {
        $this->setCacheKey([$contentTypeSlug, $format] + $params);
        $this->setCacheTags($this->getTags($contentTypeSlug));

        return $this->execute([parent::class, __FUNCTION__], [$contentTypeSlug, $params, $field, $format]);
    }
}
