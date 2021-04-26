<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;

class SnippetWidget extends BaseWidget
{
    protected $name;
    protected $type;
    protected $targets;
    protected $zone;
    protected $priority;

    public function __construct(
        string $snippet = '<!-- snippet -->',
        string $name = 'Nameless Snippet',
        array $targets = [Target::NOWHERE],
        string $zone = RequestZone::NOWHERE
    ) {
        $this->setTemplate($snippet);
        $this->setName($name);
        $this->setTargets($targets);
        $this->setZone($zone);
    }

    protected function run(array $params = []): ?string
    {
        return $this->getTemplate();
    }
}
