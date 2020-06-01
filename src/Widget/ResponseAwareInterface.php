<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Symfony\Component\HttpFoundation\Response;

/**
 * Interface ResponseAwareInterface - Widgets that make use of the Response to provide
 * their functionality need to implement this interface.
 */
interface ResponseAwareInterface extends WidgetInterface
{
    public function setResponse(Response $response);

    public function getResponse(): Response;
}
