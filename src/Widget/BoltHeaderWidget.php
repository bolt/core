<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Snippet\RequestZone;
use Bolt\Snippet\Target;

class BoltHeaderWidget extends BaseWidget
{
    protected $name = 'Bolt Header Widget';
    protected $type = 'snippet';
    protected $target = Target::NOWHERE;
    protected $zone = RequestZone::FRONTEND;

    public function __invoke(?string $template = null): string
    {
        $this->getResponse()->headers->set('X-Powered-By', 'Bolt', false);

        return '';
    }
}
