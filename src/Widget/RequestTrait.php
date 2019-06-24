<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Exception\WidgetException;
use Symfony\Component\HttpFoundation\Request;

trait RequestTrait
{
    /** @var Request */
    private $request;

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest(): Request
    {
        if ($this->request === null) {
            throw new WidgetException("Widget {$this->getName()} does not have Request set");
        }
        return $this->request;
    }
}
