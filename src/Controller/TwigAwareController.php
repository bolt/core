<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use Bolt\Entity\Field\TemplateselectField;
use Bolt\Snippet\Manager;
use Bolt\Snippet\Zone;
use Bolt\Version;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;

class TwigAwareController extends AbstractController
{
    /** @var Config */
    protected $config;

    /** @var Environment */
    protected $twig;

    /** @var Manager */
    private $snippetManager;

    /** @var Request */
    private $request;

    /**
     * @required
     */
    public function setAutowire(Config $config, Environment $twig, Manager $snippetManager, RequestStack $requestStack): void
    {
        $this->config = $config;
        $this->twig = $twig;
        $this->snippetManager = $snippetManager;
        $this->request = $requestStack->getCurrentRequest();

        $this->snippetManager->registerBoltSnippets();
    }

    /**
     * Renders a view.
     *
     * @final
     *
     * @param string|array $template
     *
     * @throws \Twig_Error_Loader  When none of the templates can be found
     * @throws \Twig_Error_Syntax  When an error occurred during compilation
     * @throws \Twig_Error_Runtime When an error occurred during rendering
     */
    protected function renderTemplate($template, array $parameters = [], ?Response $response = null): Response
    {
        // Set config and version.
        $parameters['config'] = $parameters['config'] ?? $this->config;
        $parameters['version'] = $parameters['version'] ?? Version::VERSION;
        $parameters['user'] = $parameters['user'] ?? $this->getUser();

        // Resolve string|array of templates into the first one that is found.
        if (is_array($template)) {
            $templates = (new Collection($template))
                ->map(function ($element): ?string {
                    if ($element instanceof TemplateselectField) {
                        return $element->__toString();
                    }
                    return $element;
                })
                ->filter()
                ->toArray();
            $template = $this->twig->resolveTemplate($templates);
        }

        $content = $this->twig->render($template, $parameters);

//        dd(Zone::isFrontend($this->request));

        if ($response === null) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }
}
