<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Bolt\Entity\Field\Excerptable;
use Bolt\Entity\Field\ImageField;
use Bolt\Repository\ContentRepository;
use Bolt\Utils\Excerpt;
use Bolt\Utils\Html;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tightenco\Collect\Support\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ContentExtension extends AbstractExtension
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ContentRepository
     */
    private $contentRepository;

    public function __construct(UrlGeneratorInterface $urlGenerator, ContentRepository $contentRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->contentRepository = $contentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFilter('title', [$this, 'getTitle'], $safe),
            new TwigFilter('image', [$this, 'getImage'], $safe),
            new TwigFilter('excerpt', [$this, 'getExcerpt'], $safe),
            new TwigFilter('previous', [$this, 'getPreviousContent']),
            new TwigFilter('next', [$this, 'getNextContent']),
            new TwigFilter('link', [$this, 'getLink'], $safe),
            new TwigFilter('edit_link', [$this, 'getEditLink'], $safe),
            new TwigFilter('taxonomies', [$this, 'getTaxonomies']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('previous_record', [$this, 'getPreviousContent']),
            new TwigFunction('next_record', [$this, 'getNextContent']),
        ];
    }

    public function getTitle(Content $content): string
    {
        $titleParts = [];

        foreach ($this->guessTitleFields($content) as $fieldName) {
            $titleParts[] = $content->getField($fieldName)->__toString();
        }

        return trim(implode(' ', $titleParts));
    }

    private function guessTitleFields(Content $content): array
    {
        $definition = $content->getDefinition();

        // First, see if we have a "title format" in the Content Type.
        if ($definition !== null && $definition->has('title_format')) {
            $names = $definition->get('title_format');

            $namesCollection = Collection::wrap($names)->filter(function (string $name) use ($content): bool {
                if ($content->hasFieldDefined($name) === false) {
                    throw new \RuntimeException(sprintf(
                        "Content '%s' has field '%s' added to title_format config option, but the field is not present in Content's definition.",
                        $content->getContentTypeName(),
                        $name
                    ));
                }
                return $content->hasField($name);
            });

            if ($namesCollection->isNotEmpty()) {
                return $namesCollection->values()->toArray();
            }
        }

        // Alternatively, see if we have a field named 'title' or somesuch.
        $names = ['title', 'name', 'caption', 'subject']; // English
        $names = array_merge($names, ['titel', 'naam', 'kop', 'onderwerp']); // Dutch
        $names = array_merge($names, ['nom', 'sujet']); // French
        $names = array_merge($names, ['nombre', 'sujeto']); // Spanish

        foreach ($names as $name) {
            if ($content->hasField($name)) {
                return (array) $name;
            }
        }

        return [];
    }

    /**
     * @return array|string|null
     */
    public function getImage(Content $content, bool $onlyPath = false)
    {
        foreach ($content->getFields() as $field) {
            if ($field instanceof ImageField) {
                return $onlyPath ? $field->getPath() : $field->getValue();
            }
        }

        return null;
    }

    /**
     * @param string|array|null $focus
     */
    public function getExcerpt(Content $content, int $length = 150, bool $includeTitle = true, $focus = null): string
    {
        $excerptParts = [];

        if ($includeTitle) {
            $title = $this->getTitle($content);
            if ($title !== '') {
                $title = Html::trimText($title, $length);
                $length -= mb_strlen($title);
                $excerptParts[] = $title;
            }
        }

        $skipFields = $this->guessTitleFields($content);

        foreach ($content->getFields() as $field) {
            if ($field instanceof Excerptable && in_array($field->getName(), $skipFields, true) === false) {
                $excerptPart = $field->__toString();
                if ($excerptPart !== '') {
                    $excerptParts[] = $excerptPart;
                }
            }
        }

        $specialChars = ['.', ',', '!', '?'];
        $excerpt = array_reduce($excerptParts, function (string $excerpt, string $part) use ($specialChars): string {
            if (in_array(mb_substr($part, -1), $specialChars, true) === false) {
                // add comma add end of string if it doesn't have sentence end
                $part .= '.';
            }
            return $excerpt . $part . ' ';
        }, '');

        return Excerpt::getExcerpt(rtrim($excerpt, '. '), $length, $focus);
    }

    public function getPreviousContent(Content $content, string $byColumn = 'id', bool $sameContentType = true): ?Content
    {
        return $this->getAdjacentContent($content, 'previous', $byColumn, $sameContentType);
    }

    public function getNextContent(Content $content, string $byColumn = 'id', bool $sameContentType = true): ?Content
    {
        return $this->getAdjacentContent($content, 'next', $byColumn, $sameContentType);
    }

    private function getAdjacentContent(Content $content, string $direction, string $byColumn = 'id', bool $sameContentType = true): ?Content
    {
        if ($byColumn !== 'id') {
            // @todo implement ordering by other columns/fields too
            throw new \RuntimeException('Ordering content by column other than ID is not yet implemented');
        }

        $byColumn = filter_var($byColumn, FILTER_SANITIZE_STRING);
        $contentType = $sameContentType ? $content->getContentType() : null;

        return $this->contentRepository->findAdjacentBy($byColumn, $direction, $content->getId(), $contentType);
    }

    public function getLink(Content $content, bool $absolute = false): ?string
    {
        if ($content->getId() === null) {
            return null;
        }

        return $this->urlGenerator->generate('record', [
            'slugOrId' => $content->getSlug() ?: $content->getId(),
            'contentTypeSlug' => $content->getContentTypeSlug(),
        ], $absolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    public function getEditLink(Content $content, bool $absolute = false): ?string
    {
        if ($content->getId() === null) {
            return null;
        }

        return $this->urlGenerator->generate(
            'bolt_content_edit',
            [
                'id' => $content->getId(),
            ],
            $absolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

    public function getTaxonomies(Content $content): Collection
    {
        $taxonomies = [];
        foreach ($content->getTaxonomies() as $t) {
            $link = $this->urlGenerator->generate('taxonomy', [
                'taxonomyslug' => $t->getType(),
                'slug' => $t->getSlug(),
            ]);

            $taxonomy = [
                'slug' => $t->getSlug(),
                'name' => $t->getName(),
                'link' => $link,
            ];

            if ($t->getSortorder()) {
                $taxonomy['sortorder'] = $t->getSortorder();
            }

            $taxonomies[$t->getType()][$t->getSlug()] = $taxonomy;
        }

        return new Collection($taxonomies);
    }
}
