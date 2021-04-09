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

        // If the ContentType has 'viewless_listing' set to `true`, we throw a 404.
        if ($contentType->get('viewless_listing') === true) {
            throw new NotFoundHttpException('Content is not viewable');
        }

        // If the locale is the wrong locale
        if (! $this->validLocaleForContentType($contentType)) {
            return $this->redirectToDefaultLocale();
        }

        $page = (int) $this->getFromRequest('page', '1');
        $amountPerPage = $contentType->get('listing_records');
        $params = $this->parseQueryParams($this->request, $contentType);

        /** @var Content|Pagerfanta $content */
        $content = $this->query->getContent($contentTypeSlug, $params);

        // If we're foolishly trying to "list" a singleton, we're getting a single Content here
        if ($content instanceof Content) {
            $route = $content->getDefinition()->get('record_route');
            $controller = $this->container->get('router')->getRouteCollection()->get($route)->getDefault('_controller');

            return $this->forward($controller, ['slugOrId' => $content->getId()]);
        }

        $records = $this->setRecords($content, $amountPerPage, $page);

        // Set canonical URL
        $this->canonical->setPath(
            'listing_locale',
            array_merge([
                'contentTypeSlug' => $contentType->get('slug'),
                '_locale' => $this->request->getLocale(),
            ], $params)
        );

        // Render
        $templates = $this->templateChooser->forListing($contentType);
        $this->twig->addGlobal('records', $records);

        $twigVars = [
            'records' => $records,
            $contentType->getSlug() => $records,
            'contenttype' => $contentType,
        ];

        return $this->render($templates, $twigVars);
    }

    private function parseQueryParams(Request $request, ContentType $contentType): array
    {
        if ($this->config->get('general/query_search') === false) {
            return [
                'order' => $contentType->get('order'),
                'status' => 'published',
            ];
        }

        $queryParams = collect($request->query->all());

        // Note, we're not including 'limit', 'printquery', 'returnsingle' or 'returnmultiple' on purpose
        $allowedParams = array_merge(
            $contentType['fields']->keys()->all(),
            $contentType['taxonomy']->all(),
            ['order', 'earliest', 'latest', 'offset', 'page', 'random', 'author', 'anyField', 'anything']
        );

        $params = $queryParams->mapWithKeys(function ($value, $key) use ($allowedParams) {
            // Ensure we don't have arrays, if we get something like `title[]=…` passed in.
            if (is_array($value)) {
                $value = current($value);
            }

            if (str::endsWith($key, '--like')) {
                $key = str::removeLast($key, '--like');
                $value = '%' . $value . '%';
            }

            return in_array($key, $allowedParams, true) ? [$key => $value] : [];
        })->toArray();

        if (! array_key_exists('order', $params)) {
            $params['order'] = $contentType->get('order');
        }

        // Ensure we only list things that are 'published'
        $params['status'] = 'published';

        return $params;
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
