<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Bolt\Helpers\Excerpt;
use Bolt\Repository\ContentRepository;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Twig_Markup;

trait ContentMagicTrait
{
    /**
     * Set the "Magic properties for automagic population in the API.
     */
    public $magictitle;
    public $magicexcerpt;
    public $magicimage;
    public $magiclink;
    public $magiceditlink;

    /** @var ContentRepository */
    private $repository;

    /**
     * Injected with ObjectManagerAware
     */
    public function injectObjectManager(ObjectManager $objectManager, ClassMetadata $classMetadata): void
    {
        $this->repository = $objectManager->getRepository(self::class);
    }

    private function getRepository(): ContentRepository
    {
        return $this->repository;
    }

    public function __toString(): string
    {
        return 'Content # ' . (string) $this->getId();
    }

    /**
     * Magic getter for a record. Will return the field with $name, if it
     * exists or fall back to the `magicLink`, `magicExcerpt`, etc. methods
     * if it doesn't.
     *
     * - {{ record.title }} => Field named title, fall back to magic title
     * - {{ record.magic('title') }} => Magic title, no fallback
     * - {{ record.get('title') }} => Field named title, no fallback
     *
     * @return Field|mixed|null
     */
    public function __call(string $name, array $arguments = [])
    {
        // Prefer a field with $name
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return $field->isExcerptable() ? new Twig_Markup($field, 'UTF-8') : $field;
            }
        }

        // Fall back to a `magicFoo` method
        return $this->magic($name, $arguments);
    }

    public function magic(string $name, array $arguments = [])
    {
        $magicName = 'magic' . $name;

        if (method_exists($this, $magicName)) {
            return $this->{$magicName}(...$arguments);
        }
    }

    public function get(string $name): ?Field
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }

        return null;
    }

    public function has(string $name): bool
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    public function magicLink()
    {
        return $this->urlGenerator->generate('record', [
            'slug' => $this->getSlug(),
            'contenttypeslug' => $this->getDefinition()->get('singular_slug'),
        ]);
    }

    public function magicEditLink()
    {
        return $this->urlGenerator->generate('bolt_content_edit', ['id' => $this->getId()]);
    }

    public function magicTitleFields(): array
    {
        $definition = $this->getDefinition();

        // First, see if we have a "title format" in the contenttype.
        if ($definition->has('title_format')) {
            return (array) $definition->get('title_format');
        }

        // Alternatively, see if we have a field named 'title' or somesuch.
        $names = ['title', 'name', 'caption', 'subject']; // English
        $names = array_merge($names, ['titel', 'naam', 'kop', 'onderwerp']); // Dutch
        $names = array_merge($names, ['nom', 'sujet']); // French
        $names = array_merge($names, ['nombre', 'sujeto']); // Spanish

        foreach ($names as $name) {
            if ($this->get($name)) {
                return (array) $name;
            }
        }

        // Otherwise, grab the first field of type 'text', and assume that's the title.
        foreach ($this->getFields() as $field) {
            if ($field->getType() === 'text') {
                return [$field->getName()];
            }
        }

        return [];
    }

    public function magicTitle(): string
    {
        $titleParts = [];

        foreach ($this->magicTitleFields() as $field) {
            $titleParts[] = $this->get($field);
        }

        $title = trim(implode(' ', $titleParts));

        if (empty($title)) {
            return '(untitled)';
        }

        return $title;
    }

    public function magicImage(): array
    {
        foreach ($this->getFields() as $field) {
            if ($field->getDefinition()->get('type') === 'image') {
                return $field->getValue();
            }
        }

        return [
            'filename' => '',
            'alt' => '',
            'path' => '',
        ];
    }

    /**
     * @param string|array|null $focus
     */
    public function magicExcerpt(int $length = 150, bool $includeTitle = true, $focus = null): Twig_Markup
    {
        $excerpter = new Excerpt($this);
        $excerpt = $excerpter->getExcerpt($length, $includeTitle, $focus);

        return new Twig_Markup($excerpt, 'utf-8');
    }

    public function magicPrevious(string $byColumn = 'id', bool $sameContentType = true): ?Content
    {
        $byColumn = filter_var($byColumn, FILTER_SANITIZE_STRING);
        $repository = $this->getRepository();
        $contentType = $sameContentType ? $this->getContenttype() : null;

        return $repository->findAdjacentBy($byColumn, 'previous', $this->getId(), $contentType);
    }

    public function magicNext(string $byColumn = 'id', bool $sameContentType = true): ?Content
    {
        $byColumn = filter_var($byColumn, FILTER_SANITIZE_STRING);
        $repository = $this->getRepository();
        $contentType = $sameContentType ? $this->getContenttype() : null;

        return $repository->findAdjacentBy($byColumn, 'next', $this->getId(), $contentType);
    }
}
