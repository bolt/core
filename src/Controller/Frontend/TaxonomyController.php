<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Content\ContentTypeFactory;
use Bolt\Controller\BaseController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaxonomyController extends BaseController
{
    /**
     * @Route("/{taxonomyslug}/{slug}", methods={"GET"}, name="taxonomy", requirements={"taxonomyslug"="%bolt.requirement.taxonomies%"})
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
    public function contentListing(ContentRepository $content, Request $request, string $contenttypeslug): Response
    {
        $page = (int) $request->query->get('page', 1);

        /** @var Content $records */
        $records = $content->findAll($page);

        $contenttype = ContentTypeFactory::get($contenttypeslug, $this->config->get('contenttypes'));

        $templates = $this->templateChooser->listing($contenttype);

        return $this->renderTemplate($templates, ['records' => $records]);
    }

    /**
     * @Route("/record/{slug}", methods={"GET"}, name="record")
     *
     * @param ContentRepository $contentRepository
     * @param FieldRepository   $fieldRepository
     * @param null              $slug
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function record(ContentRepository $contentRepository, FieldRepository $fieldRepository, $slug = null): Response
    {
        if (!is_numeric($slug)) {
            $field = $fieldRepository->findOneBySlug($slug);
            $record = $field->getContent();
        } else {
            $record = $contentRepository->findOneBy(['id' => $slug]);
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
