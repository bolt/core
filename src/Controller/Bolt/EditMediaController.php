<?php

declare(strict_types=1);
/**
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Areas;
use Bolt\Configuration\Config;
use Bolt\Entity\Media;
use Bolt\Repository\MediaRepository;
use Carbon\Carbon;
use Doctrine\Common\Persistence\ObjectManager;
use PHPExif\Reader\Reader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Webmozart\PathUtil\Path;

/**
 * Class EditMediaController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EditMediaController extends AbstractController
{
    /** @var Config
     */
    private $config;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var ObjectManager */
    private $manager;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /**
     * @var MediaRepository
     */
    private $mediaRepository;
    /**
     * @var Areas
     */
    private $areas;

    /** @var Collection */
    private $mediatypes;

    /** @var Reader */
    private $exif;

    /**
     * EditMediaController constructor.
     *
     * @param Config                    $config
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param ObjectManager             $manager
     * @param UrlGeneratorInterface     $urlGenerator
     * @param MediaRepository           $mediaRepository
     * @param Areas                     $areas
     */
    public function __construct(
        Config $config,
        CsrfTokenManagerInterface $csrfTokenManager,
        ObjectManager $manager,
        UrlGeneratorInterface $urlGenerator,
        MediaRepository $mediaRepository,
        Areas $areas
    ) {
        $this->config = $config;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->manager = $manager;
        $this->urlGenerator = $urlGenerator;
        $this->mediaRepository = $mediaRepository;
        $this->areas = $areas;

        $this->exif = Reader::factory(Reader::TYPE_NATIVE);
        $this->mediatypes = $config->getMediaTypes();
    }

    /**
     * @Route("/media/edit/{id}", name="bolt_media_edit", methods={"GET"})
     *
     * @param Media|null $media
     *
     * @return Response
     */
    public function edit(Media $media = null)
    {
        $context = [
            'media' => $media,
        ];

        return $this->render('editcontent/media_edit.twig', $context);
    }

    /**
     * @Route("/media/edit/{id}", name="bolt_media_edit_post", methods={"POST"})
     *
     * @param Media|null $media
     * @param Request    $request
     *
     * @return Response
     */
    public function editPost(Media $media = null, Request $request): Response
    {
        $token = new CsrfToken('media_edit', $request->request->get('_csrf_token'));

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $post = $request->request->all();

        $media->setTitle($post['title'])
            ->setDescription($post['description'])
            ->setCopyright($post['copyright'])
            ->setOriginalFilename($post['originalFilename']);

        $this->manager->persist($media);
        $this->manager->flush();

        $this->addFlash('success', 'content.updated_successfully');

        $url = $this->urlGenerator->generate('bolt_media_edit', ['id' => $media->getId()]);

        return new RedirectResponse($url);
    }

    /**
     * @Route("/media/new", name="bolt_media_new", methods={"GET"})
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function new(Request $request): RedirectResponse
    {
        $area = $request->query->get('area');
        $basepath = $this->areas->get($area, 'basepath');
        $file = $request->query->get('file');
        $filename = $basepath . $file;

        $relPath = Path::getDirectory($file);
        $relName = Path::getFilename($file);

        $file = new SplFileInfo($filename, $relPath, $relName);

        $media = $this->createOrUpdateMedia($file, $area);

        $this->manager->persist($media);
        $this->manager->flush();

        $this->addFlash('success', 'content.created_successfully');

        $url = $this->urlGenerator->generate('bolt_media_edit', ['id' => $media->getId()]);

        return new RedirectResponse($url);
    }

    /**
     * @param string $file
     * @param string $area
     *
     * @return Media
     */
    private function createOrUpdateMedia(SplFileInfo $file, string $area): Media
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
            ->setTitle(ucwords(str_replace('-', ' ', $file->getFilename())))
            ->addAuthor($this->getUser());

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
