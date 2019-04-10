<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use Bolt\Entity\Field\TemplateselectField;
use Bolt\Snippets;
use Bolt\Version;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;

class TwigAwareController extends AbstractController
{
    /** @var Config */
    protected $config;

    /** @var Environment */
    protected $twig;

    /** @var Snippets */
    private $snippets;

    /** @var Manager */
    private $snippetManager;

    /**
     * @required
     */
    public function setAutowire(Config $config, Environment $twig, Snippets $snippets): void
    {
        $this->config = $config;
        $this->twig = $twig;
        $this->snippets = $snippets;

        $this->snippets->registerBoltSnippets();
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

        // Render the template
        $content = $this->twig->render($template, $parameters);

        // Make sure we have a Response
        if ($response === null) {
            $response = new Response();
        }
        $response->setContent($content);

        // Process the snippet Queue on the Response
        $this->snippets->processQueue($response);

        return $response;
    }
}
