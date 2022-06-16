<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\FileLocations;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Media;
use Bolt\Factory\MediaFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Security("is_granted('media_edit')")
 */
class MediaEditController extends TwigAwareController implements BackendZoneInterface
{
    use CsrfTrait;

    /** @var EntityManagerInterface */
    private $em;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var FileLocations */
    private $fileLocations;

    /** @var MediaFactory */
    private $mediaFactory;

    public function __construct(
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        FileLocations $fileLocations,
        MediaFactory $mediaFactory
    ) {
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->fileLocations = $fileLocations;
        $this->mediaFactory = $mediaFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/media/edit/{id}", name="bolt_media_edit", methods={"GET"})
     */
    public function edit(?Media $media = null): Response
    {
        $context = [
            'media' => $media,
        ];

        return $this->render('@bolt/media/edit.html.twig', $context);
    }

    /**
     * @Route("/media/edit/{id}", name="bolt_media_edit_post", methods={"POST"})
     */
    public function save(?Media $media = null): Response
    {
        $this->validateCsrf('media_edit');

        $post = $this->request->request->all();

        $media->setTitle($post['title'])
            ->setDescription($post['description'])
            ->setCopyright($post['copyright'])
            ->setOriginalFilename($post['originalFilename'])
            ->setCropX((int) $post['cropX'])
            ->setCropY((int) $post['cropY'])
            ->setCropZoom((float) $post['cropZoom']);

        $this->em->persist($media);
        $this->em->flush();

        $this->addFlash('success', 'content.updated_successfully');

        $url = $this->urlGenerator->generate('bolt_media_edit', ['id' => $media->getId()]);

        return new RedirectResponse($url);
    }

    /**
     * @Route("/media/new", name="bolt_media_new", methods={"GET"})
     */
    public function new(): RedirectResponse
    {
        $fileLocation = $this->getFromRequest('location', 'files');
        $basepath = $this->fileLocations->get($fileLocation)->getBasepath();
        $file = '/' . $this->getFromRequest('file');
        $filename = $basepath . $file;

        $relPath = Path::getDirectory('/' . $file);
        $relName = basename($file);

        $file = new SplFileInfo($filename, $relPath, $relName);

        $media = $this->mediaFactory->createOrUpdateMedia($file, $fileLocation);

        // todo: This check is terrible. We should handle new/edit routes more gracefully
        if ($media->getId() === 0) {
            $this->addFlash('success', 'content.created_successfully');
        }

        $this->em->persist($media);
        $this->em->flush();

        $url = $this->urlGenerator->generate('bolt_media_edit', ['id' => $media->getId()]);

        return new RedirectResponse($url);
    }
}
