<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\Taxonomy;
use Bolt\Enum\Statuses;
use Bolt\Event\Listener\ContentFillListener;
use Bolt\Repository\TaxonomyRepository;
use Bolt\TemplateChooser;
use Carbon\Carbon;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Tightenco\Collect\Support\Collection;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class ContentActionsController extends TwigAwareController implements BackendZone
{
    use CsrfTrait;

    /**
     * @var TaxonomyRepository
     */
    private $taxonomyRepository;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var TemplateChooser
     */
    private $templateChooser;

    /**
     * @var ContentFillListener
     */
    private $contentFillListener;

    public function __construct(
        TaxonomyRepository $taxonomyRepository,
        ObjectManager $em,
        UrlGeneratorInterface $urlGenerator,
        ContentFillListener $contentFillListener,
        TemplateChooser $templateChooser,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->taxonomyRepository = $taxonomyRepository;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->contentFillListener = $contentFillListener;
        $this->templateChooser = $templateChooser;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route("/viewsaved/{id}", name="bolt_content_edit_viewsave", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function viewSaved(Request $request, ?Content $content = null): RedirectResponse
    {
        $this->validateToken($request);

        $urlParams = [
            'slugOrId' => $content->getId(),
            'contentTypeSlug' => $content->getDefinition()->get('slug'),
        ];

        $url = $this->urlGenerator->generate('record', $urlParams);

        return new RedirectResponse($url);
    }

    /**
     * @Route("/preview/{id}", name="bolt_content_edit_preview", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function preview(Request $request, ?Content $content = null): Response
    {
        $this->validateToken($request);

        $content = $this->contentFromPost($content, $request);
        $recordSlug = $content->getDefinition()->get('singular_slug');

        $context = [
            'record' => $content,
            $recordSlug => $content,
        ];

        $templates = $this->templateChooser->forRecord($content);

        return $this->renderTemplate($templates, $context);
    }



    /**
     * @Route("/duplicate/{id}", name="bolt_content_duplicate", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function duplicate(Request $request, Content $content): Response
    {

    }

    /**
     * @Route("/status/{id}", name="bolt_content_status", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function status(Request $request, Content $content): Response
    {

    }

    /**
     * @Route("/delete/{id}", name="bolt_content_delete", methods={"POST"}, requirements={"id": "\d+"})
     */
    public function delete(Request $request, Content $content): Response
    {

    }

    private function validateToken(Request $request): void
    {
        $this->validateCsrf($request, 'editrecord');
    }


}
