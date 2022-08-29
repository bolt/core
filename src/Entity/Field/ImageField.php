<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Configuration\Content\FieldType;
use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\Media;
use Bolt\Repository\MediaRepository;
use Bolt\Utils\ThumbnailHelper;
use Countable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

/**
 * @ORM\Entity
 */
class ImageField extends Field implements FieldInterface, MediaAwareInterface, Countable, RawPersistable
{
    use FileExtrasTrait;

    public const TYPE = 'image';

    private function getFieldBase()
    {
        return [
            'filename' => '',
            'path' => '',
            'media' => '',
            'thumbnail' => '',
            'fieldname' => '',
            'alt' => '',
            'extra' => [],
            'url' => '',
            'extension' => '',
        ];
    }

    public function __toString(): string
    {
        return (string) $this->getPath();
    }

    public function getDefinition(): FieldType
    {
        $fieldTypeDefinition = parent::getDefinition();

        // Set a default label based on original key but capitalized.
        if ($fieldTypeDefinition->has('extra')) {
            $extra = $fieldTypeDefinition->get('extra');
            foreach ($extra as $extraField => $extraFieldProperty) {
                if (! $extraFieldProperty->has('label')) {
                    $extraFieldProperty['label'] = \ucwords($extraField);
                }
            }
        }

        return $fieldTypeDefinition;
    }

    public function getValue(): array
    {
        $value = array_merge($this->getFieldBase(), (array) parent::getValue() ?: []);

        // Remove cruft `0` field getting stored as JSON.
        unset($value[0]);

        $value['fieldname'] = $this->getName();

        // If the filename isn't set, we're done: return the array with placeholders
        if (! $value['filename']) {
            return $value;
        }

        // Generate a URL
        $value['path'] = $this->getPath();
        $value['url'] = $this->getUrl();
        $value['extension'] = $this->getExtension();

        $thumbPackage = new PathPackage('/thumbs/', new EmptyVersionStrategy());
        $thumbnailHelper = new ThumbnailHelper();

        $fieldDefinition = $this->getDefinition();
        $path = isset($fieldDefinition['thumbnails'])
            ? $thumbnailHelper->path(
                $this->get('filename'),
                isset($fieldDefinition['thumbnails']['size']) ? $fieldDefinition['thumbnails']['size'][0] : 400,
                isset($fieldDefinition['thumbnails']['size']) ? $fieldDefinition['thumbnails']['size'][1] : 400,
                null,
                null,
                isset($fieldDefinition['thumbnails']['cropping']) ? $fieldDefinition['thumbnails']['cropping'] : null
            )
            : $thumbnailHelper->path($this->get('filename'), 400, 400);

        $value['thumbnail'] = $thumbPackage->getUrl($path);

        return $value;
    }

    public function getLinkedMedia(MediaRepository $mediaRepository): ?Media
    {
        if ($this->get('media')) {
            return $mediaRepository->findOneBy(['id' => $this->get('media')]);
        }

        if ($this->get('filename')) {
            return $mediaRepository->findOneByFullFilename($this->get('filename'));
        }

        return null;
    }

    public function setLinkedMedia(MediaRepository $mediaRepository): void
    {
        if (! $this->get('filename')) {
            return;
        }

        $media = $mediaRepository->findOneByFullFilename($this->get('filename'));

        if ($media) {
            $this->set('media', $media->getId());
        }
    }

    public function includeAlt(): bool
    {
        // This method is used in image.html.twig to decide
        // whether to display the alt field or not.
        if (! $this->getDefinition()->has('alt')) {
            return true;
        }

        return $this->getDefinition()->get('alt') === true;
    }

    /**
     * Allows {% if file is empty %} in Twig
     * See https://twig.symfony.com/doc/3.x/tests/empty.html
     */
    public function count(): int
    {
        return empty($this->getValue()['filename']) ? 0 : 1;
    }
}
