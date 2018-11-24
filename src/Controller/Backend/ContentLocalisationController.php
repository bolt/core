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
//        die();

        /** Content $content */
        $content->getFields();

        return $this->renderTemplate('content/view_locales.html.twig', [
            'record' => $content,
        ]);
    }

}
