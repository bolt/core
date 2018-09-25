<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Bolt\Helpers\Excerpt;

trait ContentMagicTraits
{
    public function __toString(): string
    {
        return (string) 'Content # ' . $this->getId();
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
     * @param string $name
     * @param array  $arguments
     *
     * @return Field|mixed|null
     */
    public function __call(string $name, array $arguments = [])
    {
        // Prefer a field with $name
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }

        // Fall back to a `magicFoo` method
        return $this->magic($name, $arguments);
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function magic(string $name, array $arguments = [])
    {
        $magicName = 'magic' . $name;

        if (method_exists($this, $magicName)) {
            return $this->$magicName(...$arguments);
        }
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function get(string $name, array $arguments = [])
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }
    }

    public function magicLink()
    {
        $path = $this->urlGenerator->generate('record', ['slug' => $this->getSlug()]);

        return $path;
    }

    public function magicEditLink()
    {
        $path = $this->urlGenerator->generate('record', ['slug' => $this->getSlug()]);

        return $path;
    }

    public function magicTitleFields(): array
    {
        // First, see if we have a "title format" in the contenttype.
        if ($title_format = $this->getDefinition()->get('title_format')) {
            return (array) $title_format;
        }

        // Alternatively, see if we have a field named 'title' or somesuch.
        $names = ['title', 'name', 'caption', 'subject']; // English
        $names = array_merge($names, ['titel', 'naam', 'kop', 'onderwerp']); // Dutch
        $names = array_merge($names, ['nom', 'sujet']); // French
        $names = array_merge($names, ['nombre', 'sujeto']); // Spanish

        foreach ($names as $name) {
            if ($field = $this->get($name)) {
                return (array) $name;
            }
        }

        // Otherwise, grab the first field of type 'text', and assume that's the title.
        if (!empty($this->contenttype['fields'])) {
            foreach ($this->getFields() as $key => $field) {
                if ($field->getDefinition()->get('type') === 'text') {
                    return [$field->getDefinition()->get('name')];
                }
            }
        }
    }

    /**
     * @return string
     */
    public function magicTitle(): string
    {
        $title = [];

        foreach ($this->magicTitleFields() as $field) {
            $title[] = $this->get($field);
        }

        return implode(' ', $title);
    }

    public function magicImage()
    {
        return 'magic image';
    }

    public function magicExcerpt($length = 200, $includeTitle = false, $focus = null)
    {
        $excerpter = new Excerpt($this);
        $excerpt = $excerpter->getExcerpt($length, $includeTitle, $focus);

        return new \Twig_Markup($excerpt, 'utf-8');
    }

    public function magicPrevious()
    {
        return 'magic previous';
    }

    public function magicNext()
    {
        return 'magic next';
    }
}
