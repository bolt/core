<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GeneralController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class GeneralController extends BaseController
{
    /**
     * @Route("/about", name="bolt_about")
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function about(): Response
    {
        return $this->renderTemplate('pages/about.html.twig');
    }

    /**
     * @Route("/kitchensink", name="bolt_kitchensink")
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function kitchensink(): Response
    {
        $this->addFlash('success', '<strong>Well done!</strong> You successfully read this important alert message.');
        $this->addFlash('info', '<strong>Heads up!</strong> This alert needs your attention, but it\'s not super important.');
        $this->addFlash('warning', '<strong>Warning!</strong> Better check yourself, you\'re not looking too good.');
        $this->addFlash('danger', '<strong>Oh snap!</strong> Change a few things up and try submitting again.');

        $twigVars = [
            'title' => 'Kitchensink',
            'subtitle' => 'To show a number of different things, on one page',
        ];

        return $this->renderTemplate('pages/placeholder.html.twig', $twigVars);
    }
}
