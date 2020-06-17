<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Canonical;
use Bolt\Configuration\Config;
use Bolt\Entity\Field\TemplateselectField;
use Bolt\Storage\Query;
use Bolt\Utils\Sanitiser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\TwigBundle\Loader\NativeFilesystemLoader;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigAwareController extends AbstractController
{
    /** @var Config */
    protected $config;

    /** @var Environment */
    protected $twig;

    /** @var Packages */
    protected $packages;

    /** @var Canonical */
    protected $canonical;

    /** @var Sanitiser */
    protected $sanitiser;

    /** @var Request */
    protected $request;

    /**
     * @required
     */
    public function setAutowire(Config $config, Environment $twig, Packages $packages, Canonical $canonical, Sanitiser $sanitiser, RequestStack $requestStack): void
    {
        $this->config = $config;
        $this->twig = $twig;
        $this->packages = $packages;
        $this->canonical = $canonical;
        $this->sanitiser = $sanitiser;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Renders a view.
     *
     * @param string|array $template
     */
    protected function renderTemplate($template, array $parameters = [], ?Response $response = null): Response
    {
        // Set User in global Twig environment
        $parameters['user'] = $parameters['user'] ?? $this->getUser();

        // if theme.yaml was loaded, set it as global.
        if ($this->config->has('theme')) {
            $parameters['theme'] = $this->config->get('theme');
        }

        $this->setThemePackage();
        $this->setTwigLoader();

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

        return $response;
    }

    private function setTwigLoader(): void
    {
        /** @var NativeFilesystemLoader $twigLoaders */
        $twigLoaders = $this->twig->getLoader();

        $path = $this->config->getPath('theme');

        if ($this->config->get('theme/template_directory')) {
            $path .= DIRECTORY_SEPARATOR . $this->config->get('theme/template_directory');
        }

        if ($twigLoaders instanceof FilesystemLoader) {
            $twigLoaders->prependPath($path, '__main__');
        }
    }

    private function setThemePackage(): void
    {
        // get the default package, and re-add as `bolt`
        $boltPackage = $this->packages->getPackage();
        $this->packages->addPackage('bolt', $boltPackage);

        // set `theme` package, and also as 'default'
        $themePath = '/theme/' . $this->config->get('general/theme');
        $themePackage = new PathPackage($themePath, new EmptyVersionStrategy());
        $this->packages->setDefaultPackage($themePackage);
        $this->packages->addPackage('theme', $themePackage);

        // set `public` package
        $publicPackage = new PathPackage('/', new EmptyVersionStrategy());
        $this->packages->addPackage('public', $publicPackage);

        // set `files` package
        $filesPackage = new PathPackage('/files/', new EmptyVersionStrategy());
        $this->packages->addPackage('files', $filesPackage);
    }

    protected function createPager(Request $request, Query $query, string $contentType, int $pageSize, string $order)
    {
        $params = [
            'status' => '!unknown',
        ];

        if ($request->get('sortBy')) {
            $params['order'] = $this->getFromRequest('sortBy');
        } else {
            $params['order'] = $order;
        }

        if ($request->get('filter')) {
            $params['anyField'] = '%' . $this->getFromRequest('filter') . '%';
        }

        if ($request->get('taxonomy')) {
            $taxonomy = explode('=', $this->getFromRequest('taxonomy'));
            $params[$taxonomy[0]] = $taxonomy[1];
        }

        return $query->getContentForTwig($contentType, $params)
            ->setMaxPerPage($pageSize);
    }

    protected function getFromRequest(string $parameter): string
    {
        return trim($this->sanitiser->clean($this->request->get($parameter, '')));
    }
}
