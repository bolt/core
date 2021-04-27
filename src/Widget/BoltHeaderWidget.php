<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Extension\ExtensionInterface;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;

class BoltHeaderWidget extends BaseWidget implements WidgetInterface, ResponseAwareInterface
{
    use ResponseTrait;

    /** @var ExtensionInterface */
    protected $extension;

    public function __invoke(array $params = []): ?string
    {
        $this->getResponse()->headers->set('X-Powered-By', 'Bolt', false);

        return null;
    }

    public function getName(): string
    {
        return 'Bolt Header Widget';
    }

    public function getTargets(): array
    {
        return [Target::NOWHERE];
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function getZone(): string
    {
        return RequestZone::FRONTEND;
    }

    public function injectExtension(ExtensionInterface $extension): void
    {
        $this->extension = $extension;
    }
}
