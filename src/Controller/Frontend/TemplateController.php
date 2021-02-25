<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Response;

class TemplateController extends TwigAwareController implements FrontendZoneInterface
{
    /**
     * Render a template. Convenient for when we simply wish to render a template as-is. Used as a
     * fallback for the 404 or maintenance pages, for instance.
     *
     * Note: This is _not_ assigned a Route by default. If it were, it's a potential security risk,
     * since a would-be attacker could try to access template files from both the theme and bolt
     * directly.
     */
    public function template(string $templateName): Response
    {
        $templates = [$templateName];

        return $this->render($templates, []);
    }
}
