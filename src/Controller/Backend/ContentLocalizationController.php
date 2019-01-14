<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\BaseController;
use Bolt\Entity\Content;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContentLocalizationController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class ContentLocalizationController extends BaseController
{
    /**
     * @Route("/edit_locales/{id}", name="bolt_content_edit_locales", methods={"GET"})
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function locales(string $id, Request $request, Content $content): Response
    {
        $content->getFields();

        return $this->renderTemplate('content/view_locales.html.twig', [
            'record' => $content,
        ]);
    }
}
