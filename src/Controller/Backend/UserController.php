<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController extends BaseController
{
    /**
     * @Route("/users", name="bolt_users")
     */
    public function users(): Response
    {
        $twigVars = [
            'title' => 'Users & Permissions',
            'subtitle' => 'To edit users and their permissions',
        ];

        return $this->renderTemplate('pages/placeholder.html.twig', $twigVars);
    }
}
