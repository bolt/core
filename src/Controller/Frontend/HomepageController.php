<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\BaseController;
use Bolt\Entity\Content;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends BaseController
{
    /**
     * @Route("/", methods={"GET"}, name="homepage")
     */
    public function homepage(): Response
    {
        $homepage = $this->getOption('theme/homepage') ?: $this->getOption('general/homepage');

        // todo get $homepage content.

        $templates = $this->templateChooser->homepage();

        return $this->renderTemplate($templates, []);
    }
}
