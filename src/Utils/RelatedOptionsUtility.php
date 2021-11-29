<?php

namespace Bolt\Utils;

use Bolt\Entity\Content;
use Bolt\Storage\Query;

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

    public function __construct(Query $query, ContentHelper $contentHelper)
    {
        $this->query = $query;
        $this->contentHelper = $contentHelper;
    }

    /**
     * Decorated by `Bolt\Cache\RelatedOptionsUtilityCacher`
     */
    public function fetchRelatedOptions(string $contentTypeSlug, string $order, string $format, bool $required, int $maxAmount): array
    {
        $pager = $this->query->getContent($contentTypeSlug, ['order' => $order])
            ->setMaxPerPage($maxAmount)
            ->setCurrentPage(1);

        $records = iterator_to_array($pager->getCurrentPageResults());

        $options = [];

        // We need to add this as a 'dummy' option for when the user is allowed
        // not to pick an option. This is needed, because otherwise the `select`
        // would default to the first one.
        if ($required === false) {
            $options[] = [
                'key' => '',
                'value' => '',
            ];
        }

        /** @var Content $record */
        foreach ($records as $record) {
            $options[] = [
                'key' => $record->getId(),
                'value' => $this->contentHelper->get($record, $format),
            ];
        }

        return $options;
    }
}
