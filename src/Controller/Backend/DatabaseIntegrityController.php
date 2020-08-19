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
class DatabaseIntegrityController extends TwigAwareController implements BackendZoneInterface
{
    /**
     * @Route("/database-check", name="bolt_database_check")
     */
    public function check(): Response
    {
        $twigVars = [
            'title' => 'controller.database.check_title',
            'subtitle' => 'controller.database.check_subtitle',
        ];

        return $this->render('@bolt/pages/placeholder.html.twig', $twigVars);
    }

    /**
     * @Route("/database-update", name="bolt_database_update")
     */
    public function update(): Response
    {
        $twigVars = [
            'title' => 'controller.database.update_title',
            'subtitle' => 'controller.database.update_title',
        ];

        return $this->render('@bolt/pages/placeholder.html.twig', $twigVars);
    }
}
