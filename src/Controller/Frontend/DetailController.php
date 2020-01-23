<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\TwigAwareController;
use Bolt\Enum\Statuses;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Bolt\Storage\Builder\ContentBuilder;
use Bolt\Storage\Builder\Filter\GraphFilter;
use Bolt\Storage\Builder\GraphBuilder;
use Bolt\Storage\Query;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends TwigAwareController implements FrontendZone
{
    /** @var TemplateChooser */
    private $templateChooser;

    /** @var ContentRepository */
    private $contentRepository;

    /** @var FieldRepository */
    private $fieldRepository;

    private $query;

    public function __construct(
        TemplateChooser $templateChooser,
        ContentRepository $contentRepository,
        FieldRepository $fieldRepository,
        Query $query
    ) {
        $this->templateChooser = $templateChooser;
        $this->contentRepository = $contentRepository;
        $this->fieldRepository = $fieldRepository;
        $this->query = $query;
    }

    /**
     * @Route(
     *     "/api/graphql",
     *     name="graphql",
     *     methods={"GET", "POST"})
     */
    public function graph(): RedirectResponse
    {
        return $this->redirectToRoute('api_graphql_entrypoint');
    }

    /**
     * @Route(
     *     "/api/get_content",
     *     name="get_content",
     *     methods={"GET"})
     */
    public function content(Request $request): Response
    {
        $query = $request->get('query');

        if (preg_match('#[a-zA-Z0-9_]+(\/[a-zA-Z0-9_\-]+)?#', $query)) {
            $graphBuilder = new GraphBuilder();
            [$contentType, $searchValue] = explode('/', $query);

            // EXAMPLE 1
//                    $query = '
//                    query {
//                        content (filter:{slug~contains: "quo", OR:[{title~contains: "quo"}, {heading~contains: "quo"}]}) {
//                            title
//                            slug
//                        }
//                    }
//                    ';
//            $query = $graphBuilder->addContent(
//                ContentBuilder::create($contentType)
//                    ->selectFields('*')
                //                    ->addFilter(
                //                        GraphFilter::createOrFilter(
                //                            GraphFilter::createSimpleFilter('slug', 'rer'),
                //                            GraphFilter::createSimpleFilter('heading', 'rer')
                //                        )
                //                    )
//            )
//                ->getQuery();

            // EXAMPLE 2
            $query = $graphBuilder->addContent(
                ContentBuilder::create($contentType)
                    ->selectFields('slug', 'heading')
                    ->addFilter(GraphFilter::createSimpleFilter('slug', $searchValue))
            )
                ->getQuery();

            return $this->query->getContent($query);
        }

        // EXAMPLE 3
//                $query1 = '
//                    query {
//                        homepage {
//                            slug
//                            title
//                        }
//                        showcases {
//                            slug
//                            title
//                        }
//                    }
//                ';
        $graphBuilder = new GraphBuilder();

        $query = $graphBuilder->addContent(
            ContentBuilder::create('homepage')
                ->selectFields('slug', 'title'),
            ContentBuilder::create('showcases')
                ->selectFields('slug', 'title')
        )->getQuery();

        return $this->query->getContent($query);
    }

    /**
     * @Route(
     *     "/api",
     *     methods={"GET", "POST"})
     */
    public function api(): RedirectResponse
    {
        return $this->redirectToRoute('api_entrypoint');
    }

    /**
     * @Route(
     *     "/{contentTypeSlug}/{slugOrId}",
     *     name="record",
     *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%"},
     *     methods={"GET"})
     * @Route(
     *     "/{_locale}/{contentTypeSlug}/{slugOrId}",
     *     name="record_locale",
     *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%", "_locale": "%app_locales%"},
     *     methods={"GET"})
     *
     * @param string|int $slugOrId
     */
    public function record($slugOrId, ?string $contentTypeSlug = null, bool $requirePublished = true): Response
    {
        // @todo should we check content type?
        if (is_numeric($slugOrId)) {
            $record = $this->contentRepository->findOneBy(['id' => (int) $slugOrId]);
        } else {
            $record = $this->contentRepository->findOneBySlug($slugOrId);
        }

        if (! $record) {
            throw new NotFoundHttpException('Content not found');
        }

        // If the content is not 'published' we throw a 404, unless we've overridden it.
        if (($record->getStatus() !== Statuses::PUBLISHED) && $requirePublished) {
            throw new NotFoundHttpException('Content is not published');
        }

        $singularSlug = $record->getContentTypeSingularSlug();

        // Update the canonical, with the correct path
        $this->canonical->setPath(null, [
            'contentTypeSlug' => $record->getContentTypeSingularSlug(),
            'slugOrId' => $record->getSlug(),
        ]);

        $context = [
            'record' => $record,
            $singularSlug => $record,
        ];

        $templates = $this->templateChooser->forRecord($record);

        return $this->renderTemplate($templates, $context);
    }
}
