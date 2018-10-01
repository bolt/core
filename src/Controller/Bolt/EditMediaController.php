<?php

declare(strict_types=1);
/**
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Config;
use Bolt\Entity\Media;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

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

    /** @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    public function __construct(Config $config, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->config = $config;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route("/media/edit/{id}", name="bolt_media_edit", methods={"GET"})
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
     */
    public function editPost(Media $media = null, Request $request, ObjectManager $manager, UrlGeneratorInterface $urlGenerator): Response
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

        $manager->persist($media);
        $manager->flush();

        $this->addFlash('success', 'content.updated_successfully');

        $url = $urlGenerator->generate('bolt_media_edit', ['id' => $media->getId()]);

        return new RedirectResponse($url);
    }
}
