<?php

namespace Bolt\Cache;

use Bolt\Utils\RelatedOptionsUtility;

class RelatedOptionsUtilityCacher extends RelatedOptionsUtility implements CachingInterface
{
    public const CACHE_CONFIG_KEY = 'related_options';

    use CachingTrait;

    public function fetchRelatedOptions(string $contentTypeSlug, string $order, string $format, bool $required, int $maxAmount): array
    {
        $this->setCacheKey('relatedOptions_' . md5($contentTypeSlug . $order . $format . (string) $required . $maxAmount));

        return $this->execute([parent::class, __FUNCTION__], [$contentTypeSlug, $order, $format, $required, $maxAmount]);
    }
}
