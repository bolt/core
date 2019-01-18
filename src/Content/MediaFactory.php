<?php

declare(strict_types=1);

namespace Bolt\Content;

use Bolt\Configuration\Config;
use Bolt\Entity\Media;
use Bolt\Repository\MediaRepository;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator;
use PHPExif\Exif;
use PHPExif\Reader\Reader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Tightenco\Collect\Support\Collection;

class MediaFactory
{
    /** @var MediaRepository */
    private $mediaRepository;

    /** @var Config */
    private $config;

    /** @var ContainerInterface */
    private $container;

    /** @var Reader */
    private $exif;

    /** @var Generator */
    private $faker;

    /** @var Collection */
    private $mediaTypes;

    /**
     * MediaFactory constructor.
     */
    public function __construct(Config $config, MediaRepository $mediaRepository, ContainerInterface $container)
    {
        $this->config = $config;
        $this->mediaRepository = $mediaRepository;
        $this->container = $container;

        $this->exif = Reader::factory(Reader::TYPE_NATIVE);
        $this->faker = Factory::create();
        $this->mediaTypes = $config->getMediaTypes();
    }

    /**
     * @throws \Exception
     */
    public function createOrUpdateMedia(SplFileInfo $file, string $area): Media
    {
        $media = $this->mediaRepository->findOneBy([
            'area' => $area,
            'path' => $file->getRelativePath(),
            'filename' => $file->getFilename(),
        ]);

        if (! $media) {
            $media = new Media();
            $media->setFilename($file->getFilename())
                ->setPath($file->getRelativePath())
                ->setArea($area);
        }

        if (! $this->mediaTypes->contains($file->getExtension())) {
            // @todo We're throwing a generic Exception here. Needs to be handled better.
            throw new \Exception('Not a valid media type.');
        }

        $media->setType($file->getExtension())
            ->setModifiedAt(Carbon::createFromTimestamp($file->getMTime()))
            ->setCreatedAt(Carbon::createFromTimestamp($file->getCTime()))
            ->setFilesize($file->getSize())
            ->setTitle($this->faker->sentence(6, true))
            ->setAuthor($this->getUser());

        if ($this->isImage($media)) {
            $this->updateImageData($media, $file);
        }

        return $media;
    }

    private function updateImageData(Media $media, SplFileInfo $file): void
    {
        /** @var Exif|bool $exif */
        $exif = $this->exif->read($file->getRealPath());

        if ($exif instanceof Exif) {
            $media->setWidth($exif->getWidth())
                ->setHeight($exif->getHeight());

            return;
        }

        $imagesize = getimagesize($file->getRealpath());

        if ($imagesize) {
            $media->setWidth($imagesize[0])
                ->setHeight($imagesize[1]);

            return;
        }
    }

    private function isImage(Media $media): bool
    {
        return in_array($media->getType(), ['gif', 'png', 'jpg', 'svg'], true);
    }

    /**
     * @todo Refactor this out!
     *
     * @return object|string|null
     */
    protected function getUser()
    {
        if (! $this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->container->get('security.token_storage');

        $token = $tokenStorage->getToken();
        if ($token === null) {
            return null;
        }

        if (is_object($token->getUser())) {
            return $token->getUser();
        }

        // e.g. anonymous authentication
        return null;
    }

    public function createFromFilename($area, $path, $filename): Media
    {
        $target = $this->config->getPath($area, true, [$path, $filename]);
        $file = new SplFileInfo($target, $path, $filename);

        return $this->createOrUpdateMedia($file, $area);
    }
}
