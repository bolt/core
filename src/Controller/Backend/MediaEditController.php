<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\FileLocations;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Media;
use Bolt\Factory\MediaFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(attribute: 'media_edit')]
class MediaEditController extends TwigAwareController implements BackendZoneInterface
{
    use CsrfTrait;

    public function __construct(
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $urlGenerator,
        private FileLocations $fileLocations,
        private MediaFactory $mediaFactory
    ) {
        $this->urlGenerator = $this->urlGenerator;
    }

    #[Route(path: '/media/edit/{id}', name: 'bolt_media_edit', methods: [Request::METHOD_GET])]
    public function edit(?Media $media = null): Response
    {
        $context = [
            'media' => $media,
        ];

        return $this->render('@bolt/media/edit.html.twig', $context);
    }

    #[Route(path: '/media/edit/{id}', name: 'bolt_media_edit_post', methods: [Request::METHOD_POST])]
    public function save(?Media $media = null): Response
    {
        $this->validateCsrf('media_edit');

        $post = $this->getRequest()->request->all();

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

    #[Route(path: '/media/new', name: 'bolt_media_new', methods: [Request::METHOD_GET])]
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
