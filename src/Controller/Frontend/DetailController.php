<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\TwigAwareController;
use Bolt\Repository\ContentRepository;
use Bolt\Routing\DynamicRouteLoader;
use Bolt\Utils\ContentHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends TwigAwareController implements FrontendZoneInterface, DetailControllerInterface
{
    /** @var ContentRepository */
    private $contentRepository;

    /** @var ContentHelper */
    private $contentHelper;

    public function __construct(ContentRepository $contentRepository, ContentHelper $contentHelper)
    {
        $this->contentRepository = $contentRepository;
        $this->contentHelper = $contentHelper;
    }

    /**
     * @see DynamicRouteLoader for routes to this method.
     *
     * @param string|int $slugOrId
     */
    public function record($slugOrId, ?string $contentTypeSlug = null, bool $requirePublished = true, ?string $_locale = null): Response
    {
        if ($_locale === null && ! $this->getFromRequest('_locale', null)) {
            $this->request->setLocale($this->defaultLocale);
        }

        // Check if there's a record with given `$slugOrId` as slug (might be a numeric slug)
        $contentType = ContentType::factory($contentTypeSlug, $this->config->get('contenttypes'));
        $record = $this->contentRepository->findOneBySlug($slugOrId, $contentType);

        // If we're given a number `$slugOrId`, like `page/100`, check if there's a record with that number as ID.
        if (! $record && is_numeric($slugOrId)) {
            $record = $this->contentRepository->findOneBy(['id' => (int) $slugOrId]);
        }

        $this->contentHelper->setCanonicalPath($record);

        // Check if we're attempting to preview an unpublished Record
        if ($record && $this->validateSecret($this->request->get('secret', ''), (string) $record->getId())) {
            $requirePublished = false;
        }

        return $this->renderSingle($record, $requirePublished);
    }

    public function contentByFieldValue(string $contentTypeSlug, string $field, string $value, bool $requirePublished = true): Response
    {
        $contentType = ContentType::factory($contentTypeSlug, $this->config->get('contenttypes'));
        $record = $this->contentRepository->findOneByFieldValue($field, $value, $contentType);

        $this->contentHelper->setCanonicalPath($record);

        return $this->renderSingle($record);
    }
}
