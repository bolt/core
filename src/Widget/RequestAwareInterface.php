<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface RequestAwareInterface - Widgets that make use of the Request to provide
 * their functionality need to implement this interface.
 */
interface RequestAwareInterface extends WidgetInterface
{
    public function setRequest(Request $request);

    public function getRequest(): Request;
}
