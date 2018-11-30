<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Controller\BaseController;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DetailController extends BaseController
{
    public function __construct(Config $config, CsrfTokenManagerInterface $csrfTokenManager, TemplateChooser $templateChooser)
    {
        parent::__construct($config, $csrfTokenManager);

        $this->templateChooser = $templateChooser;
    }

    /**
     * @Route(
     *     "/{contenttypeslug}/{slug}",
     *     name="detail",
     *     requirements={"contenttypeslug"="%bolt.requirement.contenttypes%"},
     *     methods={"GET"})
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function record(ContentRepository $contentRepository, FieldRepository $fieldRepository, string $contenttypeslug, string $slug): Response
    {
        if (! is_numeric($slug)) {
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
