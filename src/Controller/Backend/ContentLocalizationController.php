<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Security\ContentVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Permissions for this controller follow ContentEditController - you can see the localization status if you have
 * 'view' permission on this item.
 */
class ContentLocalizationController extends TwigAwareController implements BackendZoneInterface
{
    /**
     * @Route("/edit_locales/{id}", name="bolt_content_edit_locales", methods={"GET"})
     */
    public function locales(Content $content): Response
    {
        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_VIEW, $content);
        $content->getFields();

        return $this->render('@bolt/content/view_locales.html.twig', [
            'record' => $content,
        ]);
    }
}
