<?php

declare(strict_types=1);

namespace Bolt\Content;

use Tightenco\Collect\Support\Collection;

final class ContentType extends Collection
{
    public function __call($name, $arguments)
    {
        return $this->get($name);
    }
}
