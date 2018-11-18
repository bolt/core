<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FixturesController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class FixturesController extends BaseController
{
    /**
     * @Route("/fixtures", name="bolt_fixtures")
     */
    public function omnisearch(): Response
    {
        $twigVars = [
            'title' => 'Fixtures',
            'subtitle' => 'To add Fixtures, or "Dummy Content".',
        ];

        return $this->renderTemplate('pages/placeholder.html.twig', $twigVars);
    }
}
