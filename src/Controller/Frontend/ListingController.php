<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Common\Str;
use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Storage\Query;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ListingController extends TwigAwareController implements FrontendZoneInterface
{
    /** @var Query */
    private $query;

    public function __construct(Query $query)
    {
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
    public function listing(ContentRepository $contentRepository, string $contentTypeSlug, ?string $_locale = null): Response
    {
        if ($_locale === null && ! $this->getFromRequest('_locale', null)) {
            $this->request->setLocale($this->defaultLocale);
        }

        $contentType = ContentType::factory($contentTypeSlug, $this->config->get('contenttypes'));

        // If the ContentType is 'viewless' we throw a 404.
        if ($contentType->get('viewless') === true) {
            throw new NotFoundHttpException('Content is not viewable');
        }

        // If the locale is the wrong locale
        if (! $this->validLocaleForContentType($contentType)) {
            return $this->redirectToDefaultLocale();
        }

        $page = (int) $this->getFromRequest('page', '1');
        $amountPerPage = $contentType->get('listing_records');
        $order = $this->getFromRequest('order', $contentType->get('order'));
        $queryParams = $this->parseQueryParams($this->request);

        $params = array_merge($queryParams, [
            'status' => 'published',
            'order' => $order,
        ]);

        /** @var Content|Pagerfanta $content */
        $content = $this->query->getContent($contentTypeSlug, $params);

        // If we're foolishly trying to "list" a singleton, we're getting a single Content here
        if ($content instanceof Content) {
            $route = $content->getDefinition()->get('record_route');
            $controller = $this->container->get('router')->getRouteCollection()->get($route)->getDefault('_controller');

            return $this->forward($controller, ['slugOrId' => $content->getId()]);
        }

        $records = $this->setRecords($content, $amountPerPage, $page);

        $templates = $this->templateChooser->forListing($contentType);
        $this->twig->addGlobal('records', $records);

        $twigVars = [
            'records' => $records,
            $contentType->getSlug() => $records,
            'contenttype' => $contentType,
        ];

        return $this->render($templates, $twigVars);
    }

    private function parseQueryParams(Request $request): array
    {
        if ($this->config->get('general/query_search') === false) {
            return [];
        }

        $queryParams = collect($request->query->all());

        return $queryParams->mapWithKeys(function ($value, $key) {
            // Ensure we don't have arrays, if we get something like `title[]=…` passed in.
            if (is_array($value)) {
                $value = current($value);
            }

            if (str::endsWith($key, '--like')) {
                $key = str::removeLast($key, '--like');
                $value = '%' . $value . '%';
            }

            return [$key => $value];
        })->toArray();
    }

    private function setRecords($content, int $amountPerPage, int $page): Pagerfanta
    {
        if ($content instanceof Pagerfanta) {
            $records = $content->setMaxPerPage($amountPerPage)
                ->setCurrentPage($page);
        } else {
            $records = new Pagerfanta(new ArrayAdapter([]));
        }

        return $records;
    }
}
