<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Exception\WidgetException;
use Symfony\Component\HttpFoundation\Response;

trait ResponseTrait
{
    /** @var Response */
    private $response;

    public function setResponse(Response $response): WidgetInterface
    {
        $this->response = $response;

        return $this;
    }

    public function getResponse(): Response
    {
        if ($this->response === null) {
            throw new WidgetException("Widget {$this->getName()} does not have Response set");
        }

        return $this->response;
    }
}
