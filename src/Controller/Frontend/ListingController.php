<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Content\ContentType;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ListingController extends TwigAwareController
{
    /**
     * @var TemplateChooser
     */
    private $templateChooser;

    public function __construct(Config $config, Environment $twig, TemplateChooser $templateChooser)
    {
        parent::__construct($config, $twig);

        $this->templateChooser = $templateChooser;
    }

    /**
     * @Route(
     *     "/{contentTypeSlug}",
     *     name="listing",
     *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%"},
     *     methods={"GET"})
     */
    public function listing(ContentRepository $contentRepository, Request $request, string $contentTypeSlug): Response
    {
        $page = (int) $request->query->get('page', 1);

        /** @var Content[] $records */
        $records = $contentRepository->findForPage($page);

        $contentType = ContentType::factory($contentTypeSlug, $this->config->get('contenttypes'));

        $templates = $this->templateChooser->forListing($contentType);

        return $this->renderTemplate($templates, ['records' => $records]);
    }

    /**
     * Route alias for Bolt 3 backwards compatibility
     *
     * @deprecated since 4.0
     *
     * @Route(
     *     "/{contenttypeslug}",
     *     name="contentlisting",
     *     requirements={"contenttypeslug"="%bolt.requirement.contenttypes%"},
     *     methods={"GET"})
     */
    public function contentListing(ContentRepository $contentRepository, Request $request, string $contenttypeslug): Response
    {
        return $this->listing($contentRepository, $request, $contenttypeslug);
    }
}
