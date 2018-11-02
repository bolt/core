<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;

class GeneralController extends BaseController
{
    /**
     * @Route("/about", name="bolt_about")
     */
    public function about()
    {
        return $this->renderTemplate('pages/about.twig');
    }
}
