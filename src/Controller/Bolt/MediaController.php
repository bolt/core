<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Config;
use Bolt\Entity\Media;
use Bolt\Repository\MediaRepository;
use Carbon\Carbon;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use PHPExif\Exif;
use PHPExif\Reader\Reader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\PathUtil\Path;

/**
 * Class MediaController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class MediaController extends AbstractController
{
    /** @var Config */
    private $config;

    private $mediatypes = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'pdf', 'mp3', 'tiff'];

    /** @var MediaRepository */
    private $mediaRepository;

    /** @var ObjectManager */
    private $manager;

    /** @var Reader */
    private $exif;

    /** @var \Faker\Generator */
    private $faker;

    public function __construct(Config $config, MediaRepository $mediaRepository, ObjectManager $manager)
    {
        $this->config = $config;
        $this->mediaRepository = $mediaRepository;
        $this->manager = $manager;

        $this->exif = Reader::factory(Reader::TYPE_NATIVE);
        $this->faker = Factory::create();
    }

    /**
     * @Route("/media/crawl/{area}", name="bolt_media_crawler", methods={"GET"})
     */
    public function finder($area, Request $request)
    {
        $areas = [
            'config' => [
                'name' => 'Configuration files',
                'basepath' => $this->config->path('config'),
                'show_all' => true,
            ],
            'files' => [
                'name' => 'Content files',
                'basepath' => $this->config->path('files'),
                'show_all' => false,
            ],
            'themes' => [
                'name' => 'Theme files',
                'basepath' => $this->config->path('themes'),
                'show_all' => false,
            ],
        ];

        $user = $this->getUser();

        $basepath = $areas[$area]['basepath'];

        $finder = $this->findFiles($basepath);

        foreach ($finder as $file) {
            $media = $this->createOrUpdateMedia($file, $area, $user);

            $this->manager->persist($media);
            $this->manager->flush();
        }

        dd($file);

        return $this->render('finder/finder.twig', [
            'path' => $path,
            'name' => $areas[$area]['name'],
            'area' => $area,
            'finder' => $finder,
            'parent' => $parent,
            'allfiles' => $areas[$area]['show_all'] ? $this->buildIndex($basepath) : false,
        ]);
    }

    private function findFiles($base)
    {
        $fullpath = Path::canonicalize($base);

        $glob = sprintf('*.{%s}', implode(',', $this->mediatypes));

        $finder = new Finder();
        $finder->in($fullpath)->depth('< 2')->sortByName(true)->name($glob)->files();

        return $finder;
    }

    private function createOrUpdateMedia($file, $area, $user)
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
            ->addAuthor($user);

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
}
