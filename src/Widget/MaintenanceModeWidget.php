<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;

class MaintenanceModeWidget extends BaseWidget implements TwigAwareInterface
{
    protected $name = 'Maintenance Mode';
    protected $target = Target::START_OF_BODY;
    protected $zone = RequestZone::FRONTEND;
    protected $priority = 300;

    protected function run(array $params = []): ?string
    {
        return $this->getTwig()->render('@bolt/widget/maintenance_mode.twig');
    }
}
