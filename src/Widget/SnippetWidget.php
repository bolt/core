<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;

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
        $this->setTemplate($snippet);
        $this->setName($name);
        $this->setTargets([$target]);
        $this->setZone($zone);
    }

    protected function run(array $params = []): ?string
    {
        return $this->getTemplate();
    }
}
