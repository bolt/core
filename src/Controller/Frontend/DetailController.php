<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Controller\BaseController;
use Bolt\Enum\Statuses;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     *     "/{contentTypeSlug}/{slugOrId}",
     *     name="record",
     *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%"},
     *     methods={"GET"})
     *
     * @param string|int $slugOrId
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function record(ContentRepository $contentRepository, FieldRepository $fieldRepository, string $contentTypeSlug, $slugOrId): Response
    {
        if (is_numeric($slugOrId)) {
            $record = $contentRepository->findOneBy(['id' => (int) $slugOrId]);
        } else {
            /* @todo this should search only by slug or any other unique field */
            $field = $fieldRepository->findOneBySlug($slugOrId);
            if ($field === null) {
                throw new NotFoundHttpException('Content does not exist.');
            }
            $record = $field->getContent();
        }

        if ($record->getStatus() !== Statuses::PUBLISHED) {
            throw new NotFoundHttpException('Content is not published');
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
