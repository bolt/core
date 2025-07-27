<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\Backend\ContentEditController;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Event\ContentEvent;
use Bolt\Security\ContentVoter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PreviewController extends TwigAwareController implements FrontendZoneInterface
{
    use CsrfTrait;

    public function __construct(
        private ContentEditController $contentEditController,
        private EventDispatcherInterface $dispatcher,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    /**
     * @Route("/preview/{id}", name="bolt_content_edit_preview", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function preview(?Content $content = null): Response
    {
        $this->validateCsrf('editrecord');

        $content = $this->contentEditController->contentFromPost($content);
        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_VIEW, $content);

        $event = new ContentEvent($content);
        $this->dispatcher->dispatch($event, ContentEvent::ON_PREVIEW);

        return $this->renderSingle($content, false);
    }

    /**
     * @Route("/preview/{id}", name="bolt_content_edit_get", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function previewThroughGet(int $id): RedirectResponse
    {
        $url = $this->urlGenerator->generate('bolt_content_edit', ['id' => $id]);

        return new RedirectResponse($url);
    }
}
