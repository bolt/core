<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Symfony\Component\HttpFoundation\Response;

/**
 * Interface ResponseAware - Widgets that make use of the Response to provide
 * their functionality need to implement this interface.
 */
interface ResponseAware extends WidgetInterface
{
    public function setResponse(Response $response);

    public function getResponse(): Response;
}
