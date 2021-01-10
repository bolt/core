<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TODO PERMISSIONS removed check here - check if checks in twig are set. (probably not in twig itself but in the admin_menu_array() call)
 */
class MenuPageController extends TwigAwareController implements BackendZoneInterface
{
    /**
     * @Route("/menu/{slug}", name="bolt_menupage", methods={"GET"})
     */
    public function menuPage(string $slug): Response
    {
        $twigVars = [
            'title' => ucfirst($slug),
            'slug' => $slug,
            'subtitle' => 'To show a number of different things, on one page',
        ];

        return $this->render('@bolt/pages/menupage.html.twig', $twigVars);
    }
}
