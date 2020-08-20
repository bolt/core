<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ContentLocalizationController extends TwigAwareController implements BackendZoneInterface
{
    /**
     * @Route("/edit_locales/{id}", name="bolt_content_edit_locales", methods={"GET"})
     */
    public function locales(Content $content): Response
    {
        $content->getFields();

        return $this->render('@bolt/content/view_locales.html.twig', [
            'record' => $content,
        ]);
    }
}
