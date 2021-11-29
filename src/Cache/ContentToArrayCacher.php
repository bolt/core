<?php

namespace Bolt\Cache;

use Bolt\Entity\Content;
use Bolt\Twig\JsonExtension;

class ContentToArrayCacher extends JsonExtension implements CachingInterface
{
    use CachingTrait;

    public const CACHE_CONFIG_KEY = 'content_array';

    protected function contentToArray(Content $content, string $locale = ''): array
    {
        $this->setCacheKey([$content->getCacheKey($locale)]);
        $this->setCacheTags([$content->getCacheKey()]);

        return $this->execute([parent::class, __FUNCTION__], [$content, $locale]);
    }
}