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
class FixturesController extends TwigAwareController implements BackendZoneInterface
{
    /**
     * @Route("/fixtures", name="bolt_fixtures")
     */
    public function fixtures(): Response
    {
        $twigVars = [
            'title' => 'Fixtures',
            'subtitle' => 'To add Fixtures, or "Dummy Content".',
        ];

        return $this->render('@bolt/pages/placeholder.html.twig', $twigVars);
    }
}
