<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface RequestAware - Widgets that make use of the Request to provide
 * their functionality need to implement this interface.
 */
interface RequestAware
{
    public function setRequest(Request $request): BaseWidget;

    public function getRequest(): Request;
}
