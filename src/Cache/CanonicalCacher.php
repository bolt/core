<?php

namespace Bolt\Cache;

use Bolt\Canonical;

class CanonicalCacher extends Canonical implements CachingInterface
{
    use CachingTrait;

    public const CACHE_CONFIG_KEY = 'canonical';

    public function generateLink(?string $route, ?array $params, $canonical = false): ?string
    {
        $this->setCacheKey([$route, $canonical] + $params);

        return $this->execute([parent::class, __FUNCTION__], [$route, $params, $canonical]);
    }
}
