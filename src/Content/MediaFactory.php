<?php

declare(strict_types=1);
/**
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\Content;

use Bolt\Common\Json;
use Bolt\Configuration\Config;
use Bolt\Entity\Media;
use Bolt\Media\Item;
use Bolt\Repository\MediaRepository;
use Carbon\Carbon;
use Cocur\Slugify\Slugify;
use Faker\Factory;
use PHPExif\Reader\Reader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\PathUtil\Path;

class MediaFactory
{
    /** @var MediaRepository */
    private $mediaRepository;

    /** @var Config */
    private $config;

    /** @var ContainerInterface */
    private $container;

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

    private function isImage($media)
    {
        return in_array($media->getType(), ['gif', 'png', 'jpg', 'svg'], true);
    }

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
     * @param Item $item
     * @param $params
     *
     * @return Media
     */
    public function createFromUpload(Item $item, $params): Media
    {
        if (Json::test($params)) {
            $params = Json::parse($params);
            $addedPath = $params['path'];
            $area = $params['area'];
        } else {
            $addedPath = '';
            $area = 'files';
        }

        $targetFilename = $addedPath . \DIRECTORY_SEPARATOR . $this->sanitiseFilename($item->getName());

        $source = $this->config->getPath('cache', true, ['uploads', $item->getId(), $item->getName()]);
        $target = $this->config->getPath($area, true, $targetFilename);

        $relPath = Path::getDirectory($targetFilename);
        $relName = Path::getFilename($targetFilename);

        // Move the file over
        $fileSystem = new Filesystem();
        $fileSystem->rename($source, $target, true);

        $file = new SplFileInfo($target, $relPath, $relName);

        $media = $this->createOrUpdateMedia($file, $area);

        return $media;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function sanitiseFilename(string $filename): string
    {
        $extensionSlug = new Slugify(['regexp' => '/([^a-z0-9]|-)+/']);
        $filenameSlug = new Slugify(['lowercase' => false]);

        $extension = $extensionSlug->slugify(Path::getExtension($filename));
        $filename = $filenameSlug->slugify(Path::getFilenameWithoutExtension($filename));

        return $filename . '.' . $extension;
    }
}
