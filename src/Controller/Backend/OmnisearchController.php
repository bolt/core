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
class OmnisearchController extends TwigAwareController implements BackendZone
{
    /**
     * @Route("/omnisearch", name="bolt_omnisearch")
     */
    public function omnisearch(): Response
    {
        $twigVars = [
            'title' => 'Omnisearch',
            'subtitle' => 'To search, in an omni-like fashion',
        ];

        return $this->renderTemplate('@bolt/pages/placeholder.html.twig', $twigVars);
    }
}
