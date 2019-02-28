<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Controller\TwigAwareController;
use Bolt\Enum\Statuses;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Bolt\Storage\Query\Generator\SimpleGraphGenerator;
use Bolt\Storage\Query\Query;
use Bolt\TemplateChooser;
use function GuzzleHttp\Psr7\parse_query;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class DetailController extends TwigAwareController
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

    private $simpleGraphGenerator;

    public function __construct(
        Config $config,
        Environment $twig,
        TemplateChooser $templateChooser,
        ContentRepository $contentRepository,
        FieldRepository $fieldRepository,
        Query $query,
        SimpleGraphGenerator $simpleGraphGenerator
    ) {
        $this->templateChooser = $templateChooser;
        $this->contentRepository = $contentRepository;
        $this->fieldRepository = $fieldRepository;
        $this->query = $query;
        $this->simpleGraphGenerator = $simpleGraphGenerator;
        parent::__construct($config, $twig);
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

        if (preg_match('#[a-zA-Z0-9_]+\/[a-zA-Z0-9_\-]+#', $query)) {
            return $this->query->getContentForTwig($this->simpleGraphGenerator->generate($query));
        }

        return $this->query->getContentForTwig($query);
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
     */
    public function record($slugOrId): Response
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

        dump($record);
        dump($record->getFieldValues());

        $templates = $this->templateChooser->forRecord($record);

        return $this->renderTemplate($templates, $context);
    }
}
