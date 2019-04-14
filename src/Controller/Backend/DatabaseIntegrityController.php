<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class DatabaseIntegrityController extends TwigAwareController implements BackendZone
{
    /**
     * @Route("/database-check", name="bolt_database_check")
     */
    public function check(): Response
    {
        $twigVars = [
            'title' => 'Database Check',
            'subtitle' => 'To check the Database',
        ];

        return $this->renderTemplate('@bolt/pages/placeholder.html.twig', $twigVars);
    }

    /**
     * @Route("/database-update", name="bolt_database_update")
     */
    public function update(): Response
    {
        $twigVars = [
            'title' => 'Database Update',
            'subtitle' => 'To update the Database',
        ];

        return $this->renderTemplate('@bolt/pages/placeholder.html.twig', $twigVars);
    }
}
