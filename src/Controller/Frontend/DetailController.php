<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Enum\Statuses;
use Bolt\Repository\ContentRepository;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends TwigAwareController implements FrontendZoneInterface
{
    /** @var TemplateChooser */
    private $templateChooser;

    /** @var ContentRepository */
    private $contentRepository;

    /** @var Request */
    private $request;

    public function __construct(TemplateChooser $templateChooser, ContentRepository $contentRepository, RequestStack $requestStack)
    {
        $this->templateChooser = $templateChooser;
        $this->contentRepository = $contentRepository;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @Route(
     *     "/{contentTypeSlug}/{slugOrId}",
     *     name="record",
     *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%"},
     *     methods={"GET|POST"})
     * @Route(
     *     "/{_locale}/{contentTypeSlug}/{slugOrId}",
     *     name="record_locale",
     *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%", "_locale": "%app_locales%"},
     *     methods={"GET|POST"})
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

        return $this->renderSingle($record, $requirePublished);
    }

    public function contentByFieldValue(string $contentTypeSlug, string $field, string $value): Response
    {
        $contentType = ContentType::factory($contentTypeSlug, $this->config->get('contenttypes'));
        $record = $this->contentRepository->findOneByFieldValue($field, $value, $contentType);

        // Update the canonical, with the correct path
        $this->canonical->setPath(null, [
            'field' => $field,
            'value' => $value,
        ]);

        return $this->renderSingle($record);
    }

    public function renderSingle(?Content $record, bool $requirePublished = true): Response
    {
        if (! $record) {
            throw new NotFoundHttpException('Content not found');
        }

        // If the content is not 'published' we throw a 404, unless we've overridden it.
        if (($record->getStatus() !== Statuses::PUBLISHED) && $requirePublished) {
            throw new NotFoundHttpException('Content is not published');
        }

        // If the ContentType is 'viewless' we also throw a 404.
        if (($record->getDefinition()->get('viewless') === true) && $requirePublished) {
            throw new NotFoundHttpException('Content is not viewable');
        }

        $singularSlug = $record->getContentTypeSingularSlug();

        $context = [
            'record' => $record,
            $singularSlug => $record,
        ];

        // We add the record as a _global_ variable. This way we can use that
        // later on, if we need to get the root record of a page.
        $this->twig->addGlobal('record', $record);

        $templates = $this->templateChooser->forRecord($record);

        return $this->renderTemplate($templates, $context);
    }
}
