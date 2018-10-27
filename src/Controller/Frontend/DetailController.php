<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\BaseController;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends BaseController
{
    /**
     * @Route("/{contenttypeslug}/{slug}", methods={"GET"}, name="detail", requirements={"contenttypeslug"="%bolt.requirement.contenttypes%"})
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
    public function record(ContentRepository $contentRepository, FieldRepository $fieldRepository, string $contenttypeslug, string $slug): Response
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
