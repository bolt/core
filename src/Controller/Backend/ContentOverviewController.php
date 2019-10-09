<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\TwigAwareController;
use Bolt\Repository\ContentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ContentOverviewController extends TwigAwareController implements BackendZone
{
    /**
     * @Route("/content/{contentType}", name="bolt_content_overview")
     */
    public function overview(string $contentType = ''): Response
    {
        $contentType = ContentType::factory($contentType, $this->config->get('contenttypes'));

        return $this->renderTemplate('@bolt/content/listing.html.twig', [
            'contentType' => $contentType,
        ]);
    }
}
