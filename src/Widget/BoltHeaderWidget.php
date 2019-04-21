<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;
use Symfony\Component\HttpFoundation\Response;

class BoltHeaderWidget implements WidgetInterface, ResponseAware
{
    /**
     * @var Response
     */
    private $response;

    public function __invoke(array $params = []): string
    {
        $this->getResponse()->headers->set('X-Powered-By', 'Bolt', false);

        return '';
    }

    public function getName(): string
    {
        return 'Bolt Header Widget';
    }

    public function getTarget(): string
    {
        return Target::NOWHERE;
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function getZone(): string
    {
        return RequestZone::FRONTEND;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
