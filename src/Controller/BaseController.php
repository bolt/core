<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use Bolt\TemplateChooser;
use Bolt\Version;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Tightenco\Collect\Support\Collection;

class BaseController extends AbstractController
{
    /** @var Config */
    protected $config;

    /** @var TemplateChooser */
    protected $templateChooser;

    /** @var CsrfTokenManagerInterface */
    protected $csrfTokenManager;

    public function __construct(Config $config, TemplateChooser $templateChooser, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->config = $config;
        $this->templateChooser = $templateChooser;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Renders a view.
     *
     * @final
     *
     * @param mixed         $template
     * @param array         $parameters
     * @param Response|null $response
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    protected function renderTemplate($template, array $parameters = [], Response $response = null): Response
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');

        // Set config and version.
        $parameters['config'] = $this->config;
        $parameters['version'] = Version::VERSION;

        // Resolve string|array of templates into the first one that is found.
        if ($template instanceof Collection) {
            $template = $twig->resolveTemplate($template->toArray());
        }

        $content = $twig->render($template, $parameters);

        if ($response === null) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }

    /**
     * Shortcut for {@see \Bolt\Config::get}.
     *
     * @param string $path
     * @param mixed  $default
     *
     * @return string|int|array|null
     */
    protected function getOption($path, $default = null)
    {
        return $this->config->get($path, $default);
    }
}
