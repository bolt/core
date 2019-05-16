<?php

declare(strict_types=1);

namespace Bolt\Tests\Widget;

use Bolt\Widget\BaseWidget;
use Bolt\Widget\Injector\AdditionalTarget;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\TwigAware;

class DummyWidget extends BaseWidget implements TwigAware
{
    protected $name = 'Dummy Widget';
    protected $target = AdditionalTarget::WIDGET_BACK_DASHBOARD_ASIDE_TOP;
    protected $priority = 200;
    protected $template = '@bolt/widgets/weather.twig';
    protected $zone = RequestZone::BACKEND;

}
