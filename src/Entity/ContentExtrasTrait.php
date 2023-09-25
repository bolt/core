<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Bolt\Twig\ContentExtension;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @see \Bolt\Entity\Content
 */
trait ContentExtrasTrait
{
    /** @var ContentExtension|null */
    private $contentExtension;

    public function setContentExtension(ContentExtension $contentExtension): void
    {
        $this->contentExtension = $contentExtension;
    }

    /**
     * @internal This should not be used outside of API. Use ContentExtension or Twig filters instead.
     *
     * @Groups("get_content")
     */
    public function getExtras(): array
    {
        /** @var Content $content */
        $content = $this;

        return array_filter([
            'title' => $this->contentExtension->getAnyTitle($content, 80),
            'image' => $this->contentExtension->getImage($content, true),
            'excerpt' => $this->contentExtension->getExcerpt($content),
            'listFormat' => $this->contentExtension->getListFormat($content),
            'link' => $this->contentExtension->getLink($content),
            'editLink' => $this->contentExtension->getEditLink($content),
            'statusLink' => $this->contentExtension->getStatusLink($content),
            'deleteLink' => $this->contentExtension->getDeleteLink($content),
            'duplicateLink' => $this->contentExtension->getDuplicateLink($content),
            'icon' => $this->getContentTypeIcon(),
            'name' => $this->getDefinition()->get('name'),
            'singular_name' => $this->getDefinition()->get('singular_name'),
            'feature' => $this->contentExtension->getSpecialFeature($content),
            'contentTypeOverviewLink' => $this->contentExtension->getContentTypeOverviewLink($content),
        ]);
    }
}
