<?php

namespace Bolt\Utils;

use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Storage\Query;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Utility class to get the 'Related Records' as options to show as a pull-down in the Editor.
 *
 * Decorated by `Bolt\Cache\RelatedOptionsUtilityCacher`
 */
class RelatedOptionsUtility
{
    /** @var Query */
    private $query;

    /** @var ContentHelper */
    private $contentHelper;

    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(Query $query, ContentHelper $contentHelper, UrlGeneratorInterface $router)
    {
        $this->query = $query;
        $this->contentHelper = $contentHelper;
        $this->router = $router;
    }

    /**
     * Decorated by `Bolt\Cache\RelatedOptionsUtilityCacher`
     */
    public function fetchRelatedOptions(ContentType $fromContentType, string $toContentTypeSlug, string $order, string $format, bool $required, ?bool $allowEmpty, int $maxAmount, bool $linkToRecord): array
    {
        $pager = $this->query->getContent($toContentTypeSlug, ['order' => $order])
            ->setMaxPerPage($maxAmount)
            ->setCurrentPage(1);

        $records = iterator_to_array($pager->getCurrentPageResults());
        $fromContentTypeRelationDefinition = $fromContentType->get('relations')->get($toContentTypeSlug);
        $options = [];

        // We need to add this as a 'dummy' option for when the user is allowed
        // not to pick an option. This is needed, because otherwise the `select`
        // would default to the first one.
        if (Field::settingsAllowEmpty($allowEmpty, $required)) {
            $options[] = [
                'key' => '',
                'value' => '',
            ];
        }

        /** @var Content $record */
        foreach ($records as $key => $record) {
            $options[$key] = [
                'key' => $record->getId(),
                'value' => $this->contentHelper->get($record, $format),
            ];

            // Generate URL for related record if the link_to_record option is defined in relations in the contenttypes.yaml
            if (isset($fromContentTypeRelationDefinition['link_to_record'])) {
                if ($fromContentTypeRelationDefinition['link_to_record']) {
                    $options[$key]["link_to_record_url"] = $this->router->generate('bolt_content_edit', ['id' => $record->getId()]);
                }
            }
        }

        return $options;
    }
}
