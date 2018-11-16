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

    /**
     * @Route("/kitchensink", name="bolt_kitchensink")
     */
    public function kitchensink()
    {
        $this->addFlash('success', "<strong>Well done!</strong> You successfully read this important alert message.");
        $this->addFlash('info', "<strong>Heads up!</strong> This alert needs your attention, but it's not super important.");
        $this->addFlash('warning', "<strong>Warning!</strong> Better check yourself, you're not looking too good.");
        $this->addFlash('danger', "<strong>Oh snap!</strong> Change a few things up and try submitting again.");

        return $this->renderTemplate('pages/about.twig');
    }
}
