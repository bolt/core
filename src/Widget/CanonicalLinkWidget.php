<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Canonical;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;

class CanonicalLinkWidget extends BaseWidget
{
    protected $name = 'Canonical Link';
    protected $target = Target::END_OF_HEAD;
    protected $zone = RequestZone::FRONTEND;
    protected $priority = 200;

    /** @var Canonical */
    private $canonical;

    public function __construct(Canonical $canonical)
    {
        $this->canonical = $canonical;
    }

    protected function run(array $params = []): ?string
    {
        return sprintf('<link rel="canonical" href="%s">', $this->canonical->get());
    }
}
