<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\TwigAwareController;
use Bolt\Enum\Statuses;
use Bolt\Repository\ContentRepository;
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

    public function __construct(TemplateChooser $templateChooser, ContentRepository $contentRepository)
    {
        $this->templateChooser = $templateChooser;
        $this->contentRepository = $contentRepository;
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
            $contentType = ContentType::factory($contentTypeSlug, $this->config->get('contenttypes'));
            $record = $this->contentRepository->findOneBySlug($slugOrId, $contentType);
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
