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
    /**
     * @var ContentExtension
     */
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

        return [
            'title' => $this->contentExtension->getTitle($content),
            'image' => $this->contentExtension->getImage($content),
            'excerpt' => $this->contentExtension->getExcerpt($content),
            'link' => $this->contentExtension->getLink($content),
            'editLink' => $this->contentExtension->getEditLink($content),
            'statusLink' => $this->contentExtension->getStatusLink($content),
            'deleteLink' => $this->contentExtension->getDeleteLink($content),
            'duplicateLink' => $this->contentExtension->getDuplicateLink($content),
            'icon' => $this->getIcon(),
        ];
    }
}
