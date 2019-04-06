<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Snippet\Target;
use Bolt\Snippet\Zone;

class CanonicalLinkWidget extends BaseWidget
{
    protected $name = 'Weather Widget';
    protected $type = 'widget';
    protected $target = Target::END_OF_HEAD;
    protected $zone = Zone::FRONTEND;
    protected $priority = 200;
}
