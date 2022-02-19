<?php

namespace Bolt\Routing;

use Bolt\Configuration\Config;
use Bolt\Controller\Backend\ContentEditController;
use Bolt\Controller\Frontend\DetailController;
use Bolt\Controller\Frontend\ListingController;
use Bolt\Controller\Frontend\TaxonomyController;
use Symfony\Bundle\FrameworkBundle\Routing\RouteLoaderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines routes for content types and taxonomies based on their names. It does so by loading content types
 * and taxonomies from the config, and creating routes for every name.
 *
 * @see Config
 */
class DynamicRouteLoader implements RouteLoaderInterface
{
    /** @var Config $config */
    private $config;

    /** @var string $locales '|' separated string of locales enabled in this Bolt application */
    private $locales;

    /**
     * @param string $locales '|' separated string of locales, see services.yaml
     */
    public function __construct(Config $config, string $locales)
    {
        $this->config = $config;
        $this->locales = $locales;
    }

    public function loadRoutes(): RouteCollection
    {
        $routes = new RouteCollection();

        $contentTypes = $this->config->get('contenttypes');
        $contentTypeSlugs = $contentTypes
            ->pluck('slug')
            ->concat($contentTypes->pluck('singular_slug'))
            ->unique()
            ->implode('|');

        $taxonomies = $this->config->get('taxonomies');
        $taxonomySlugs = $taxonomies
            ->pluck('slug')
            ->concat($taxonomies->pluck('singular_slug'))
            ->unique()
            ->implode('|');

        // ListingController::listing (front-end)
        /*
         * OLD method annotations, for reference, not in use.
         *
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
        $listingControllerDefaults = [
            '_controller' => ListingController::class . '::listing',
        ];
        $routes->add('listing',
            (new Route('/{contentTypeSlug}',
                $listingControllerDefaults,
                [
                    'contentTypeSlug' => $contentTypeSlugs
                ])
            )->setMethods(['GET', 'POST'])
        );

        $routes->add('listing_locale',
            (new Route(
                '/{_locale}/{contentTypeSlug}/{slugOrId}',
                $listingControllerDefaults,
                [
                    'contentTypeSlug' => $contentTypeSlugs,
                    '_locale' => $this->locales,
                ])
            )->setMethods(['GET', 'POST'])
        );

        // DetailController::record() (front-end)
        /*
         * OLD method annotations, for reference, not in use.
         *
         * @Route(
         *     "/{contentTypeSlug}/{slugOrId}",
         *     name="record",
         *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%"},
         *     methods={"GET|POST"})
         * @Route(
         *     "/{_locale}/{contentTypeSlug}/{slugOrId}",
         *     name="record_locale",
         *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%", "_locale": "%app_locales%"},
         *     methods={"GET|POST"})
         */
        $datailControllerDefaults = [
            '_controller' => DetailController::class . '::record',
        ];
        $routes->add('record',
            (new Route(
                '/{contentTypeSlug}/{slugOrId}',
                $datailControllerDefaults,
                [
                    'contentTypeSlug' => $contentTypeSlugs
                ])
            )->setMethods(['GET', 'POST'])
        );
        $routes->add(
            'record_locale',
            (new Route(
                '/{_locale}/{contentTypeSlug}/{slugOrId}',
                $datailControllerDefaults,
                [
                    'contentTypeSlug' => $contentTypeSlugs,
                    '_locale' => $this->locales,
                ])
            )->setMethods(['GET', 'POST'])
        );

        // TaxonomyController::listing() (front-end)
        /*
         * OLD method annotations, for reference, not in use.
         *
         * @Route(
         *     "/{taxonomyslug}/{slug}",
         *     name="taxonomy",
         *     requirements={"taxonomyslug"="%bolt.requirement.taxonomies%"},
         *     methods={"GET|POST"}
         * )
         * @Route(
         *     "/{_locale}/{taxonomyslug}/{slug}",
         *     name="taxonomy_locale",
         *     requirements={"taxonomyslug"="%bolt.requirement.taxonomies%", "_locale": "%app_locales%"},
         *     methods={"GET|POST"}
         * )
         */
        $taxonomyControllerDefaults = [
            '_controller' => TaxonomyController::class . '::listing',
        ];
        $routes->add('taxonomy',
            (new Route(
                '/{taxonomyslug}/{slug}',
                $taxonomyControllerDefaults,
                [
                    'taxonomyslug' => $taxonomySlugs
                ])
            )->setMethods(['GET', 'POST'])
        );
        $routes->add(
            'taxonomy_locale',
            (new Route(
                '/{_locale}/{taxonomyslug}/{slug}',
                $taxonomyControllerDefaults,
                [
                    'taxonomyslug' => $taxonomySlugs,
                    '_locale' => $this->locales,
                ])
            )->setMethods(['GET', 'POST'])
        );

        // routes for ContentEditController::editFromSlug
        /*
         * OLD method annotations, for reference, not in use.
         *
         * @Route(
         *     "/edit/{_locale}/{contentTypeSlug}/{slugOrId}",
         *     name="bolt_edit_content_slug",
         *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%"},
         *     methods={"GET"})
         * @Route(
         *     "/edit/{contentTypeSlug}/{slugOrId}",
         *     name="bolt_edit_content_slug",
         *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%"},
         *     methods={"GET"})
         */
        $contentEditControllerDefaults = [
            '_controller' => ContentEditController::class . '::editFromSlug',
        ];
        $routes->add('taxonomy',
            (new Route(
                '/{taxonomyslug}/{slug}',
                $contentEditControllerDefaults,
                [
                    ['contentTypeSlug' => $contentTypeSlugs]
                ])
            )->setMethods(['GET', 'POST'])
        );
        $routes->add(
            'taxonomy_locale',
            (new Route(
                '/{_locale}/{taxonomyslug}/{slug}',
                $contentEditControllerDefaults,
                [
                    'contentTypeSlug' => $contentTypeSlugs,
                    '_locale' => '.*',
                ])
            )->setMethods(['GET', 'POST'])
        );

        return $routes;
    }
}
