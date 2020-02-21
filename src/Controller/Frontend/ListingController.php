<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\TwigAwareController;
use Bolt\Repository\ContentRepository;
use Bolt\Storage\Query;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingController extends TwigAwareController implements FrontendZoneInterface
{
    /** @var TemplateChooser */
    private $templateChooser;

    /** @var Query */
    private $query;

    public function __construct(TemplateChooser $templateChooser, Query $query)
    {
        $this->templateChooser = $templateChooser;
        $this->query = $query;
    }

    /**
     * @Route(
     *     "/{contentTypeSlug}",
     *     name="listing",
     *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%"},
     *     methods={"GET|POST"})
     * @Route(
     *     "/{_locale}/{contentTypeSlug}",
     *     name="listing_locale",
     *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%", "_locale": "%app_locales%"},
     *     methods={"GET|POST"})
     */
    public function listing(ContentRepository $contentRepository, Request $request, string $contentTypeSlug): Response
    {
        $contentType = ContentType::factory($contentTypeSlug, $this->config->get('contenttypes'));
        $page = (int) $request->query->get('page', 1);
        $amountPerPage = $contentType->get('listing_records');

        $records = $this->query->getContent($contentTypeSlug, ['status' => 'published'])
            ->setMaxPerPage($amountPerPage)
            ->setCurrentPage($page);

        $templates = $this->templateChooser->forListing($contentType);

        $twigVars = [
            'records' => $records,
            $contentType->getSlug() => $records,
            'contenttype' => $contentType,
        ];

        return $this->renderTemplate($templates, $twigVars);
    }
}
