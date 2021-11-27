<?php

namespace Bolt\Cache;

use Bolt\Utils\RelatedOptionsUtility;

class RelatedOptionsUtilityCacher extends RelatedOptionsUtility implements CachingInterface
{
    use CachingTrait;

    public const CACHE_CONFIG_KEY = 'related_options';

    public function fetchRelatedOptions(string $contentTypeSlug, string $order, string $format, bool $required, int $maxAmount): array
    {
        $this->setCacheKey([$contentTypeSlug, $order, $format, (string) $required, $maxAmount]);

        return $this->execute([parent::class, __FUNCTION__], [$contentTypeSlug, $order, $format, $required, $maxAmount]);
    }
}
