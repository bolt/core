<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use Bolt\Content\ContentTypeFactory;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Bolt\TemplateChooser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tightenco\Collect\Support\Collection;

class FrontendController extends AbstractController
{
    /** @var Config */
    private $config;

    /** @var TemplateChooser */
    private $templateChooser;

    public function __construct(Config $config, TemplateChooser $templateChooser)
    {
        $this->config = $config;
        $this->templateChooser = $templateChooser;
    }

    /**
     * @Route("/", methods={"GET"}, name="homepage")
     */
    public function homepage(): Response
    {
        $homepage = $this->getOption('theme/homepage') ?: $this->getOption('general/homepage');

        // todo get $homepage content.

        $templates = $this->templateChooser->homepage();

        return $this->renderTemplate($templates, []);
    }

    /**
     * @Route("/content", methods={"GET"}, name="listing")
     *
     * @param ContentRepository $content
     * @param Request           $request
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function contentListing(ContentRepository $content, Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);

        /** @var Content $records */
        $records = $content->findLatest($page);

        $contenttype = ContentTypeFactory::get('pages', $this->config->get('contenttypes'));

        $templates = $this->templateChooser->listing($contenttype);

        return $this->renderTemplate($templates, ['records' => $records]);
    }

    /**
     * @Route("/record/{id<[1-9]\d*>}", methods={"GET"}, name="record_by_id")
     * @Route("/record/{slug<[a-z0-9_-]+>}", methods={"GET"}, name="record")
     *
     * @param ContentRepository $contentRepository
     * @param FieldRepository   $fieldRepository
     * @param null              $id
     * @param null              $slug
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function record(ContentRepository $contentRepository, FieldRepository $fieldRepository, $id = null, $slug = null): Response
    {
        if ($id) {
            $record = $contentRepository->findOneBy(['id' => $id]);
        } elseif ($slug) {
            $field = $fieldRepository->findOneBySlug($slug);
            $record = $field->getContent();
        }

        $recordSlug = $record->getDefinition()['singular_slug'];

        $context = [
            'record' => $record,
            $recordSlug => $record,
        ];

        $templates = $this->templateChooser->record($record);

        return $this->renderTemplate($templates, $context);
    }

    /**
     * Shortcut for {@see \Bolt\Config::get}.
     *
     * @param string $path
     * @param mixed  $default
     *
     * @return string|int|array|null
     */
    protected function getOption($path, $default = null)
    {
        return $this->config->get($path, $default);
    }

    /**
     * Renders a view.
     *
     * @final
     *
     * @param Collection    $templates
     * @param array         $parameters
     * @param Response|null $response
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    protected function renderTemplate(Collection $templates, array $parameters = [], Response $response = null): Response
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');

        $parameters['config'] = $this->config;

        // Resolve string|array of templates into the first one that is found.
        $template = $twig->resolveTemplate($templates->toArray());

        $content = $twig->render($template, $parameters);

        if ($response === null) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }
}
