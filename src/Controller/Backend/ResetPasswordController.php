<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ResetPasswordController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class ResetPasswordController extends BaseController
{
    /**
     * @Route("/resetpassword", name="bolt_resetpassword")
     */
    public function omnisearch(): Response
    {
        $twigVars = [
            'title' => 'Reset Password',
            'subtitle' => 'To reset your password, if you\'ve misplaced it',
        ];

        return $this->renderTemplate('pages/placeholder.html.twig', $twigVars);
    }
}
