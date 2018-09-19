<?php

declare(strict_types=1);

namespace Bolt\Entity;

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
            return $this->$magicName($arguments);
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

    public function magicTitle()
    {
        return 'magic title';
    }

    public function magicImage()
    {
        return 'magic image';
    }

    public function magicExcerpt()
    {
        return 'magic excerpt';
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
