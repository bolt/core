<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends TwigAwareController implements FrontendZone
{
    /**
     * Render a template. Convenient for when we simply wish to render a template as-is. Used as a
     * fallback for the 404 or maintenance pages, for instance.
     *
     * Note: This is _not_ assigned a Route by default. If it were, it's a potential security risk,
     * since a would-be attacker could try to access template files from both the theme and bolt
     * directly.
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function template(string $templateName): Response
    {
        $templates = [$templateName];

        return $this->renderTemplate($templates, []);
    }
}
