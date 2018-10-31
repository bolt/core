<?php

declare(strict_types=1);

namespace Bolt\Content;

use Bolt\Configuration\Config;
use Bolt\Entity\Media;
use Bolt\Repository\MediaRepository;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator;
use PHPExif\Reader\Reader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\SplFileInfo;
use Tightenco\Collect\Support\Collection;
use Webmozart\PathUtil\Path;

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
    private $mediatypes;

    /**
     * MediaFactory constructor.
     *
     * @param Config             $config
     * @param MediaRepository    $mediaRepository
     * @param ContainerInterface $container
     */
    public function __construct(Config $config, MediaRepository $mediaRepository, ContainerInterface $container)
    {
        $this->config = $config;
        $this->mediaRepository = $mediaRepository;
        $this->container = $container;

        $this->exif = Reader::factory(Reader::TYPE_NATIVE);
        $this->faker = Factory::create();
        $this->mediatypes = $config->getMediaTypes();
    }

    /**
     * @param SplFileInfo $file
     * @param string      $area
     *
     * @return Media
     */
    public function createOrUpdateMedia(SplFileInfo $file, string $area): Media
    {
        $media = $this->mediaRepository->findOneBy([
            'area' => $area,
            'path' => $file->getRelativePath(),
            'filename' => $file->getFilename(), ]);

        if (!$media) {
            $media = new Media();
            $media->setFilename($file->getFilename())
                ->setPath($file->getRelativePath())
                ->setArea($area);
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

    /**
     * @param Media $media
     * @param $file
     */
    private function updateImageData(Media $media, $file)
    {
        /** @var Exif $exif */
        $exif = $this->exif->read($file->getRealPath());

        if ($exif) {
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

    /**
     * @param $media
     *
     * @return bool
     */
    private function isImage($media)
    {
        return in_array($media->getType(), ['gif', 'png', 'jpg', 'svg'], true);
    }

    /**
     * @return object|string|void
     */
    protected function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }

    /**
     * @param $area
     * @param $path
     * @param $filename
     *
     * @return Media
     */
    public function createFromFilename($area, $path, $filename): Media
    {
        $target = $this->config->getPath($area, true, [$path, $filename]);
        $file = new SplFileInfo($target, $path, $filename);

        $media = $this->createOrUpdateMedia($file, $area);

        return $media;
    }
}
