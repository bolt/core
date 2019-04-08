<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Snippet\Target;
use Bolt\Snippet\Zone;

class BoltHeaderWidget extends BaseWidget
{
    protected $name = 'Bolt Header Widget';
    protected $type = 'snippet';
    protected $target = Target::NOWHERE;
    protected $zone = Zone::FRONTEND;

    public function invoke(?string $template = null): string
    {
        $this->response->headers->set('X-Powered-By', 'Bolt', false);

        return '';
    }
}
