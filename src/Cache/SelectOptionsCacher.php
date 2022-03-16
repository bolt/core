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

    /**
     * Make sure something like `(pages,entries)` becomes an array like ['pages', 'entries']
     */
    private function getTags(string $contentTypeSlug): array
    {
        $tags = explode(',', $contentTypeSlug);

        $tags = array_map(function($t) {
            return preg_replace('/[^\pL\d,]+/u', '', $t);
        }, $tags);

        return $tags;
    }
}
