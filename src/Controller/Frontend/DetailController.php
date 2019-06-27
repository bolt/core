<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\TwigAwareController;
use Bolt\Enum\Statuses;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Bolt\Storage\Query\Builder\ContentBuilder;
use Bolt\Storage\Query\Builder\Filter\GraphFilter;
use Bolt\Storage\Query\Builder\GraphBuilder;
use Bolt\Storage\Query\Query;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends TwigAwareController implements FrontendZone
{
    /**
     * @var TemplateChooser
     */
    private $templateChooser;

    /**
     * @var ContentRepository
     */
    private $contentRepository;

    /**
     * @var FieldRepository
     */
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
//                        content (filter:{slug_contains: "quo", OR:[{title~contains: "quo"}, {heading~contains: "quo"}]}) {
//                            title
//                            slug
//                        }
//                    }
//                    ';
            $query = $graphBuilder->addContent(
                ContentBuilder::create($contentType)
                    ->selectFields('*')
                //                    ->addFilter(
                //                        GraphFilter::createOrFilter(
                //                            GraphFilter::createSimpleFilter('slug', 'rer'),
                //                            GraphFilter::createSimpleFilter('heading', 'rer')
                //                        )
                //                    )
            )
                ->getQuery();

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
     *
     * @param string|int $slugOrId
     */
    public function record($slugOrId, ?string $contentTypeSlug = null): Response
    {
        // @todo should we check content type?
        if (is_numeric($slugOrId)) {
            $record = $this->contentRepository->findOneBy(['id' => (int) $slugOrId]);
        } else {
            // @todo this should search only by slug or any other unique field
            $field = $this->fieldRepository->findOneBySlug($slugOrId);
            if ($field === null) {
                throw new NotFoundHttpException('Content does not exist.');
            }
            $record = $field->getContent();
        }

        if ($record->getStatus() !== Statuses::PUBLISHED) {
            throw new NotFoundHttpException('Content is not published');
        }

        $recordSlug = $record->getDefinition()->get('singular_slug');

        $context = [
            'record' => $record,
            $recordSlug => $record,
        ];

        $templates = $this->templateChooser->forRecord($record);

        return $this->renderTemplate($templates, $context);
    }
}
