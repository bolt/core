<?php

declare(strict_types=1);
/**
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Areas;
use Bolt\Configuration\Config;
use Bolt\Content\MediaFactory;
use Bolt\Controller\BaseController;
use Bolt\Entity\Media;
use Bolt\Repository\MediaRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
class EditMediaController extends BaseController
{
    /** @var ObjectManager */
    private $manager;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var MediaRepository */
    private $mediaRepository;

    /** @var Areas */
    private $areas;

    /** @var MediaFactory */
    private $mediaFactory;

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
        Areas $areas,
        MediaFactory $mediaFactory
    ) {
        $this->config = $config;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->manager = $manager;
        $this->urlGenerator = $urlGenerator;
        $this->mediaRepository = $mediaRepository;
        $this->areas = $areas;
        $this->mediaFactory = $mediaFactory;
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

        return $this->renderTemplate('editcontent/media_edit.twig', $context);
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

        $media = $this->mediaFactory->createOrUpdateMedia($file, $area);

        $this->manager->persist($media);
        $this->manager->flush();

        $this->addFlash('success', 'content.created_successfully');

        $url = $this->urlGenerator->generate('bolt_media_edit', ['id' => $media->getId()]);

        return new RedirectResponse($url);
    }
}
