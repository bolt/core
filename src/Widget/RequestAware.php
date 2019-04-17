<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Symfony\Component\HttpFoundation\Request;

/**
 * Trait RequestAware - Widgets that make use of the Request to provide
 * their functionality need to use this trait.
 */
trait RequestAware
{
    /** @var Request */
    protected $request;

    public function setRequest(Request $request): WidgetInterface
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
