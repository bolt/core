<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Snippet\RequestZone;
use Bolt\Snippet\Target;

class SnippetWidget extends BaseWidget
{
    protected $name = 'Nameless Snippet';
    protected $type = 'snippet';
    protected $target = Target::NOWHERE;
    protected $zone = RequestZone::NOWHERE;
    protected $priority = 200;

    public function __construct(
        ?string $snippet = null,
        ?string $name = null,
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
