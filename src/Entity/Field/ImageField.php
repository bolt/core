<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\Media;
use Bolt\Repository\MediaRepository;
use Bolt\Utils\ThumbnailHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ORM\Entity
 */
class ImageField extends Field implements FieldInterface, MediaAwareInterface
{
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
            'url' => '',
        ];
    }

    public function __toString(): string
    {
        return $this->getPath();
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

        $thumbPackage = new PathPackage('/thumbs/', new EmptyVersionStrategy());
        $thumbnailHelper = new ThumbnailHelper();

        $path = $thumbnailHelper->path($this->get('filename'), 400, 400);
        $value['thumbnail'] = $thumbPackage->getUrl($path);

        return $value;
    }

    private function getPath(): string
    {
        $filesPackage = new PathPackage('/files/', new EmptyVersionStrategy());

        return $filesPackage->getUrl($this->get('filename'));
    }

    private function getUrl(): string
    {
        $request = Request::createFromGlobals();

        return sprintf(
            '%s://%s%s',
            $request->getScheme(),
            $this->getHost($request),
            $this->getPath()
        );
    }

    private function getHost($request)
    {
        $host = $request->server->get('CANONICAL_HOST', $request->getHost());
        $scheme = $request->getScheme();
        $port = $request->getPort();

        if (($scheme === 'http' && $port === 80) || ($scheme === 'https' && $port === 443)) {
            return $host;
        }

        return $host . ':'.$port;
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
        $media = $mediaRepository->findOneByFullFilename($this->get('filename'));

        if ($media) {
            $this->set('media', $media->getId());
        }
    }

    public function includeAlt(): bool
    {
        if (! $this->getDefinition()->has('alt')) {
            return true;
        }

        return $this->getDefinition()->get('alt') === true;
    }
}
