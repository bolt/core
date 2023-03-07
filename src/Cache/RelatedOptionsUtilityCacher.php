<?php

namespace Bolt\Cache;

use Bolt\Configuration\Content\ContentType;
use Bolt\Utils\RelatedOptionsUtility;

class RelatedOptionsUtilityCacher extends RelatedOptionsUtility implements CachingInterface
{
    use CachingTrait;

    public const CACHE_CONFIG_KEY = 'related_options';

    public function fetchRelatedOptions(ContentType $fromContentType, string $contentTypeSlug, string $order, string $format, bool $required, ?bool $allowEmpty, int $maxAmount, bool $linkToRecord): array
    {
        $this->setCacheKey([$contentTypeSlug, $order, $format, (string) $required, $maxAmount]);
        $this->setCacheTags($this->getTags($contentTypeSlug));

        return $this->execute([parent::class, __FUNCTION__], [$fromContentType, $contentTypeSlug, $order, $format, $required, $allowEmpty, $maxAmount, $linkToRecord]);
    }
}
