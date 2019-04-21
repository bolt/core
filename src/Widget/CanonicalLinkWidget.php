<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Snippet\RequestZone;
use Bolt\Snippet\Target;

class CanonicalLinkWidget extends BaseWidget
{
    protected $name = 'Canonical Link';
    protected $target = Target::NOWHERE;
    protected $zone = RequestZone::NOWHERE;
    protected $priority = 200;
}
