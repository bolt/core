<?php

declare(strict_types=1);

namespace Bolt\Snippets;

class CanonicalLinkWidget extends BaseWidget
{
    protected $name = 'Weather Widget';
    protected $type = 'widget';
    protected $target = Target::END_OF_HEAD;
    protected $zone = Zone::FRONTEND;
    protected $priority = 200;
}
