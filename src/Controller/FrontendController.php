<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Content\ContentTypeFactory;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends BaseController
{
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
}
