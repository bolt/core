<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\BaseController;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Carbon\Carbon;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * Class ContentLocalisationController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class ContentLocalisationController extends BaseController
{
    /**
     * @Route("/edit_locales/{id}", name="bolt_content_edit_locales", methods={"GET"})
     *
     * @param string $id
     * @param Request $request
     * @param Content $content
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function locales(string $id, Request $request, Content $content): Response
    {
        dump($content);
        die();

        /** Content $content */
        $content->getFields();

        return $this->renderTemplate('content/edit.html.twig', [
            'record' => $content,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="bolt_content_edit_post", methods={"POST"})
     */
    public function editPost(Request $request, ObjectManager $manager, UrlGeneratorInterface $urlGenerator, ?Content $content = null): Response
    {
        $token = new CsrfToken('editrecord', $request->request->get('_csrf_token'));

        if (! $this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $content = $this->contentFromPost($content, $request);

        $manager->persist($content);
        $manager->flush();

        $this->addFlash('success', 'content.updated_successfully');

        $url = $urlGenerator->generate('bolt_content_edit', ['id' => $content->getId()]);

        return new RedirectResponse($url);
    }

    private function contentFromPost(?Content $content, Request $request): Content
    {
        $post = $request->request->all();

        if (! $content) {
            $content = new Content();
            $content->setAuthor($this->getUser());
            $content->setContentType($request->attributes->get('id'));
            $content->setConfig($this->config);
        }

        $content->setStatus(current($post['status']));
        $content->setPublishedAt(new Carbon($post['publishedAt']));
        $content->setDepublishedAt(new Carbon($post['depublishedAt']));

        foreach ($post['fields'] as $key => $postfield) {
            $this->updateFieldFromPost($key, $postfield, $content);
        }

        return $content;
    }

    private function updateFieldFromPost(string $key, $postfield, Content $content): void
    {
        if ($content->hasField($key)) {
            $field = $content->getField($key);
        } else {
            $fields = collect($content->getDefinition()->get('fields'));
            $field = Field::factory($fields->get($key)['type']);
            $field->setName($key);
            $content->addField($field);
        }

        $field->setValue((array) $postfield);
    }
}
