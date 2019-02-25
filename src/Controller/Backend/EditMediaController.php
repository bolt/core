<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Areas;
use Bolt\Configuration\Config;
use Bolt\Content\MediaFactory;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Media;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;
use Webmozart\PathUtil\Path;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class EditMediaController extends TwigAwareController
{
    use CsrfTrait;

    /** @var ObjectManager */
    private $em;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Areas */
    private $areas;

    /** @var MediaFactory */
    private $mediaFactory;

    public function __construct(
        ObjectManager $em,
        UrlGeneratorInterface $urlGenerator,
        Areas $areas,
        MediaFactory $mediaFactory,
        CsrfTokenManagerInterface $csrfTokenManager,
        Config $config,
        Environment $twig
    ) {
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->areas = $areas;
        $this->mediaFactory = $mediaFactory;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        parent::__construct($config, $twig);
    }

    /**
     * @Route("/media/edit/{id}", name="bolt_media_edit", methods={"GET"})
     */
    public function edit(?Media $media = null): Response
    {
        $context = [
            'media' => $media,
        ];

        return $this->renderTemplate('@bolt/editcontent/media_edit.html.twig', $context);
    }

    /**
     * @Route("/media/edit/{id}", name="bolt_media_edit_post", methods={"POST"})
     */
    public function save(Request $request, ?Media $media = null): Response
    {
        $this->validateCsrf($request, 'media_edit');

        $post = $request->request->all();

        $media->setTitle($post['title'])
            ->setDescription($post['description'])
            ->setCopyright($post['copyright'])
            ->setOriginalFilename($post['originalFilename']);

        $this->em->persist($media);
        $this->em->flush();

        $this->addFlash('success', 'content.updated_successfully');

        $url = $this->urlGenerator->generate('bolt_media_edit', ['id' => $media->getId()]);

        return new RedirectResponse($url);
    }

    /**
     * @Route("/media/new", name="bolt_media_new", methods={"GET"})
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

        $this->em->persist($media);
        $this->em->flush();

        $this->addFlash('success', 'content.created_successfully');

        $url = $this->urlGenerator->generate('bolt_media_edit', ['id' => $media->getId()]);

        return new RedirectResponse($url);
    }
}
