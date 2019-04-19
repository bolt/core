<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Snippet\RequestZone;
use Bolt\Snippet\Target;

class SnippetWidget extends BaseWidget
{
    protected $name;
    protected $type;
    protected $target;
    protected $zone;
    protected $priority;

    public function __construct(
        string $snippet = '<!-- snippet -->',
        string $name = 'Nameless Snippet',
        string $target = Target::NOWHERE,
        string $zone = RequestZone::NOWHERE
    ) {
        $this->template = $snippet;
        $this->name = $name;
        $this->target = $target;
        $this->zone = $zone;
    }

    public function __invoke(?string $template = null): string
    {
        return $this->getTemplate();
    }
}
