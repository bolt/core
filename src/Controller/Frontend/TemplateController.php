<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\TwigAwareController;
use Bolt\Enum\Statuses;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends TwigAwareController implements FrontendZone
{
    /** @var TemplateChooser */
    private $templateChooser;

    /** @var ContentRepository */
    private $contentRepository;

    /** @var FieldRepository */
    private $fieldRepository;

    public function __construct(TemplateChooser $templateChooser, ContentRepository $contentRepository, FieldRepository $fieldRepository)
    {
        $this->templateChooser = $templateChooser;
        $this->contentRepository = $contentRepository;
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * Render a template. Convenient for when we simply wish to render a template as-is. Used as a
     * fallback for the 404 or maintenance pages, for instance.
     *
     * Note: This is _not_ assigned a Route by default. If it were, it's a potential security risk,
     * since a would-be attacker could try to access template files from both the theme and bolt
     * directly.
     *
     * @param string $templateName
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function template(string $templateName): Response
    {

        $templates = [ $templateName ];

        return $this->renderTemplate($templates, []);
    }
}
