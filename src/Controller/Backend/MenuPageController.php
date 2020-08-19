<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
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
