<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Bolt\Twig\ContentExtension;
use Symfony\Component\Serializer\Annotation\Groups;

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
        ];
    }

    public function jsonSerialize(): array
    {
        /** @var Content $content */
        $content = $this;

        if ($this->getDefinition() === null) {
            return [];
        }

        return [
            'id' => $content->getId(),
            'contentType' => $content->getContentType(),
            'slug' => $content->getSlug(),
            'author' => [
                'id' => $content->getAuthor()->getId(),
                'displayName' => $content->getAuthor()->getDisplayName(),
                'username' => $content->getAuthor()->getUsername(),
                'email' => $content->getAuthor()->getEmail(),
            ],
            'fields' => $content->getFieldValues(),
            'taxonomies' => $content->getTaxonomyValues(),
            'extras' => $this->getExtras(),
            'status' => $content->getStatus(),
            'icon' => $content->getIcon(),
            'createdAt' => $content->getCreatedAt(),
            'modifiedAt' => $content->getModifiedAt(),
            'publishedAt' => $content->getPublishedAt(),
            'depublishedAt' => $content->getDepublishedAt(),
        ];
    }
}
