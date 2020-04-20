<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\TwigAwareController;
use Bolt\Repository\ContentRepository;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends TwigAwareController implements FrontendZoneInterface
{
    /** @var TemplateChooser */
    private $templateChooser;

    public function __construct(TemplateChooser $templateChooser)
    {
        $this->templateChooser = $templateChooser;
    }

    /**
     * @Route("/", methods={"GET|POST"}, name="homepage")
     * @Route(
     *     "/{_locale}/",
     *     methods={"GET|POST"},
     *     name="homepage_locale",
     *     requirements={"_locale": "%app_locales%"},
     *     defaults={"canonical": true })
     */
    public function homepage(ContentRepository $contentRepository): Response
    {
        $homepage = $this->config->get('theme/homepage') ?: $this->config->get('general/homepage');
        $params = explode('/', $homepage);

        // @todo Get $homepage content, using "setcontent"
        $record = $contentRepository->findOneBy([
            'contentType' => $params[0],
            'id' => $params[1],
        ]);
        if (! $record) {
            $record = $contentRepository->findOneBy(['contentType' => $params[0]]);
        }

        $templates = $this->templateChooser->forHomepage();

        return $this->renderTemplate($templates, ['record' => $record]);
    }
}
