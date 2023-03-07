<?php

namespace Bolt\Cache;

use Bolt\Entity\Content;
use Bolt\Utils\ContentHelper;

class GetFormatCacher extends ContentHelper implements CachingInterface
{
    use CachingTrait;

    public const CACHE_CONFIG_KEY = 'formatter';

    public function get(Content $record, string $format = '', ?string $locale = null): string
    {
        $this->setCacheKey([$record->getId(), $format, $locale]);
        $this->setCacheTags($this->getTags($record->getContentTypeSlug()));

        return $this->execute([parent::class, __FUNCTION__], [$record, $format, $locale]);
    }
}
