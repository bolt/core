<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Content\ContentType;
use Bolt\Controller\BaseController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class TaxonomyController extends BaseController
{
    public function __construct(Config $config, CsrfTokenManagerInterface $csrfTokenManager, TemplateChooser $templateChooser)
    {
        parent::__construct($config, $csrfTokenManager);

        $this->templateChooser = $templateChooser;
    }

    /**
     * @Route("
     *     /{taxonomyslug}/{slug}",
     *     name="taxonomy",
     *     requirements={"taxonomyslug"="%bolt.requirement.taxonomies%"},
     *     methods={"GET"}
     * )
     */
    public function listing(ContentRepository $contentRepository, Request $request, string $taxonomyslug, string $slug): Response
    {
        $page = (int) $request->query->get('page', 1);

        /** @var Content[] $records */
        $records = $contentRepository->findForPage($page);

        $contentType = ContentType::factory('page', $this->config->get('contenttypes'));

        $templates = $this->templateChooser->listing($contentType);

        return $this->renderTemplate($templates, ['records' => $records]);
    }
}
