<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Controller\BaseController;
use Bolt\Entity\Content;
use Bolt\TemplateChooser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class PreviewController extends BaseController
{
    public function __construct(Config $config, CsrfTokenManagerInterface $csrfTokenManager, TemplateChooser $templateChooser)
    {
        parent::__construct($config, $csrfTokenManager);

        $this->templateChooser = $templateChooser;
    }

    /**
     * @Route("/preview", methods={"GET", "POST"}, name="preview")
     */
    public function preview(): Response
    {
        $homepage = $this->getOption('theme/homepage') ?: $this->getOption('general/homepage');

        // @todo Get $homepage content.
        $twigvars = [
            'record' => $homepage,
        ];

        $templates = $this->templateChooser->homepage();

        return $this->renderTemplate($templates, $twigvars);
    }
}
