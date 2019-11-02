<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\TwigAwareController;
use Bolt\Enum\Statuses;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Bolt\TemplateChooser;
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

    public function __construct(TemplateChooser $templateChooser, ContentRepository $contentRepository, FieldRepository $fieldRepository)
    {
        $this->templateChooser = $templateChooser;
        $this->contentRepository = $contentRepository;
        $this->fieldRepository = $fieldRepository;
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
            // @todo this should search only by slug or any other unique field
            $field = $this->fieldRepository->findOneBySlug($slugOrId);
            if ($field === null) {
                throw new NotFoundHttpException('Content does not exist.');
            }
            $record = $field->getContent();
        }

        // If the content is not 'published' we throw a 404, unless we've overridden it.
        if (($record->getStatus() !== Statuses::PUBLISHED) && $requirePublished) {
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
