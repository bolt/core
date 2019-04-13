<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Snippet\Target;
use Bolt\Snippet\Zone;

class CanonicalLinkWidget extends BaseWidget
{
    protected $name = 'Canonical Link';
    protected $type = 'widget';
    protected $target = Target::NOWHERE;
    protected $zone = Zone::NOWHERE;
    protected $priority = 200;
}
