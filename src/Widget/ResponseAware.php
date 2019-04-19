<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Symfony\Component\HttpFoundation\Response;

/**
 * Interface ResponseAware - Widgets that make use of the Response to provide
 * their functionality need to implement this interface.
 */
interface ResponseAware
{
    public function setResponse(?Response $response = null): WidgetInterface;

    public function getResponse(): ?Response;
}
