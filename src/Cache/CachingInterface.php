<?php

namespace Bolt\Cache;

interface CachingInterface
{
    public function getCacheKey(): string;

    public function setCacheKey(array $tokens): void;

    public function execute(callable $fn, array $params = []);
}
