<?php

declare(strict_types=1);

namespace Bolt\Content;

use Bolt\Configuration\Config;
use Bolt\Controller\UserTrait;
use Bolt\Entity\Media;
use Bolt\Repository\MediaRepository;
use Carbon\Carbon;
use PHPExif\Exif;
use PHPExif\Reader\Reader;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tightenco\Collect\Support\Collection;

class MediaFactory
{
    use UserTrait;

    /** @var MediaRepository */
    private $mediaRepository;

    /** @var Config */
    private $config;

    /** @var Reader */
    private $exif;

    /** @var Collection */
    private $mediaTypes;

    public function __construct(Config $config, MediaRepository $mediaRepository, TokenStorageInterface $tokenStorage)
    {
        $this->config = $config;
        $this->mediaRepository = $mediaRepository;
        $this->tokenStorage = $tokenStorage;

        $this->exif = Reader::factory(Reader::TYPE_NATIVE);
        $this->mediaTypes = $config->getMediaTypes();
    }

    public function createOrUpdateMedia(SplFileInfo $file, string $area, ?string $title = null): Media
    {
        $media = $this->mediaRepository->findOneBy([
            'area' => $area,
            'path' => $file->getRelativePath(),
            'filename' => $file->getFilename(),
        ]);

        if ($media === null) {
            $media = new Media();
            $media->setFilename($file->getFilename())
                ->setPath($file->getRelativePath())
                ->setArea($area);
        }

        if ($this->mediaTypes->contains($file->getExtension()) === false) {
            throw new UnsupportedMediaTypeHttpException("{$file->getExtension()} files are not accepted");
        }

        $media->setType($file->getExtension())
            ->setModifiedAt(Carbon::createFromTimestamp($file->getMTime()))
            ->setCreatedAt(Carbon::createFromTimestamp($file->getCTime()))
            ->setFilesize($file->getSize())
            ->setTitle($title ?? $file->getFilename())
            ->setAuthor($this->getUser());

        if ($this->isImage($media)) {
            $this->updateImageDimensions($media, $file);
        }

        return $media;
    }

    private function updateImageDimensions(Media $media, SplFileInfo $file): void
    {
        $exif = $this->exif->read($file->getRealPath());

        if ($exif instanceof Exif) {
            $media->setWidth($exif->getWidth())
                ->setHeight($exif->getHeight());

            return;
        }

        $size = getimagesize($file->getRealpath());

        if ($size !== false) {
            $media->setWidth($size[0])
                ->setHeight($size[1]);

            return;
        }
    }

    private function isImage(Media $media): bool
    {
        return in_array($media->getType(), ['gif', 'png', 'jpg', 'svg'], true);
    }

    public function createFromFilename($area, $path, $filename): Media
    {
        $target = $this->config->getPath($area, true, [$path, $filename]);
        $file = new SplFileInfo($target, $path, $filename);

        return $this->createOrUpdateMedia($file, $area);
    }
}
