<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Config;
use Bolt\Content\FieldFactory;
use Bolt\Entity\Content;
use Bolt\Version;
use Carbon\Carbon;
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
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class EditRecordController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EditRecordController extends AbstractController
{
    /** @var Config */
    private $config;

    /** @var Version */
    private $version;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    public function __construct(Config $config, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->config = $config;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route("/edit/{id}", name="bolt_edit_record", methods={"GET"})
     */
    public function edit(Content $content = null, Request $request): Response
    {
        if (!$content) {
            $content = new Content();
            $content->setAuthor($this->getUser());
        }

        return $this->render('editcontent/edit.twig', [
            'record' => $content,
         ]);
    }

    /**
     * @Route("/edit/{id}", name="bolt_edit_record_post", methods={"POST"})
     */
    public function edit_post(Content $content = null, Request $request, ObjectManager $manager, UrlGeneratorInterface $urlGenerator): Response
    {
        $token = new CsrfToken('editrecord', $request->request->get('_csrf_token'));

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $content = $this->contentFromPost($content, $request);

        $manager->persist($content);
        $manager->flush();

        $this->addFlash('success', 'content.updated_successfully');

        $url = $urlGenerator->generate('bolt_edit_record', ['id' => $content->getId()]);

        return new RedirectResponse($url);
    }

    private function contentFromPost(Content $content = null, Request $request)
    {
        $post = $request->request->all();

        if (!$content) {
            $content = new Content();
            $content->setAuthor($this->getUser());
        }

        $content->setStatus($post['status']);
        $content->setPublishedAt(new Carbon($post['publishedAt']));
        $content->setDepublishedAt(new Carbon($post['depublishedAt']));

        foreach ($post['fields'] as $key => $postfield) {
            $this->updateFieldFromPost($key, $postfield, $content);
        }

        return $content;
    }

    private function updateFieldFromPost(string $key, $postfield, Content $content)
    {
        if (!$field = $content->getField($key)) {
            $field = FieldFactory::get('text');
            $fields[] = $field;
        }

        $field->setValue((array) $postfield);
    }
}
