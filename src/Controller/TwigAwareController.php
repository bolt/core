<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Canonical;
use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Entity\Field\TemplateselectField;
use Bolt\Enum\Statuses;
use Bolt\Storage\Query;
use Bolt\TemplateChooser;
use Bolt\Twig\CommonExtension;
use Bolt\Utils\Sanitiser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;
use Twig\Loader\ChainLoader;
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

    /** @var TemplateChooser */
    protected $templateChooser;

    /** @var string */
    protected $defaultLocale;

    /** @var CommonExtension */
    private $commonExtension;

    /**
     * @required
     */
    public function setAutowire(Config $config, Environment $twig, Packages $packages, Canonical $canonical, Sanitiser $sanitiser, RequestStack $requestStack, TemplateChooser $templateChooser, string $defaultLocale, CommonExtension $commonExtension): void
    {
        $this->config = $config;
        $this->twig = $twig;
        $this->packages = $packages;
        $this->canonical = $canonical;
        $this->sanitiser = $sanitiser;
        $this->request = $requestStack->getCurrentRequest();
        $this->templateChooser = $templateChooser;
        $this->defaultLocale = $defaultLocale;
        $this->commonExtension = $commonExtension;
    }

    /**
     * Renders a view.
     *
     * @param string|array $template
     */
    public function render($template, array $parameters = [], ?Response $response = null): Response
    {
        // Set User in global Twig environment
        $parameters['user'] = $parameters['user'] ?? $this->getUser();

        // if theme.yaml was loaded, set it as global.
        if ($this->config->has('theme')) {
            $parameters['theme'] = $this->config->get('theme');
        }

        $content = $this->renderTemplate($template, $parameters);

        // Make sure we have a Response
        if ($response === null) {
            $response = new Response();
        }
        $response->setContent($content);

        return $response;
    }

    /**
     * Renders a single record.
     */
    public function renderSingle(?Content $record, bool $requirePublished = true, array $templates = []): Response
    {
        if (! $record) {
            throw new NotFoundHttpException('Content not found');
        }

        // If the content is not 'published' we throw a 404, unless we've overridden it.
        if (($record->getStatus() !== Statuses::PUBLISHED) && $requirePublished) {
            throw new NotFoundHttpException('Content is not published');
        }

        // If the ContentType is 'viewless' we also throw a 404.
        if (($record->getDefinition()->get('viewless') === true) && $requirePublished) {
            throw new NotFoundHttpException('Content is not viewable');
        }

        // If the locale is the wrong locale
        if (! $this->validLocaleForContentType($record->getDefinition())) {
            return $this->redirectToDefaultLocale();
        }

        $singularSlug = $record->getContentTypeSingularSlug();

        $context = [
            'record' => $record,
            $singularSlug => $record,
        ];

        // We add the record as a _global_ variable. This way we can use that
        // later on, if we need to get the root record of a page.
        $this->twig->addGlobal('record', $record);

        if (empty($templates)) {
            $templates = $this->templateChooser->forRecord($record);
        }

        return $this->render($templates, $context);
    }

    protected function validLocaleForContentType(ContentType $contentType): bool
    {
        if ($contentType->isKeyNotEmpty('locales')) {
            return $contentType->get('locales')->contains($this->request->getLocale());
        }

        return $this->request->getLocale() === $this->defaultLocale;
    }

    protected function redirectToDefaultLocale(): ?Response
    {
        $this->request->getSession()->set('_locale', $this->defaultLocale);

        $params = $this->request->attributes->get('_route_params');

        if (isset($params['_locale'])) {
            $params['_locale'] = $this->defaultLocale;
        }

        return $this->redirectToRoute($this->request->get('_route'), $params);
    }

    private function setTwigLoader(): void
    {
        /** @var FilesystemLoader|ChainLoader $twigLoaders */
        $twigLoaders = $this->twig->getLoader();

        $twigLoaders = $twigLoaders instanceof ChainLoader ?
            $twigLoaders->getLoaders() :
            [$twigLoaders];

        $path = $this->config->getPath('theme');

        if ($this->config->get('theme/template_directory')) {
            $path .= DIRECTORY_SEPARATOR . $this->config->get('theme/template_directory');
        }

        foreach ($twigLoaders as $twigLoader) {
            if ($twigLoader instanceof FilesystemLoader) {
                $twigLoader->prependPath($path, '__main__');
            }
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

    /**
     * Renders a template, with theme support.
     */
    public function renderTemplate($template, array $parameters = []): string
    {
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

        return $this->twig->render($template, $parameters);
    }

    public function createPager(Query $query, string $contentType, int $pageSize, string $order)
    {
        $params = [
            'status' => '!unknown',
            'returnmultiple' => true,
        ];

        if ($this->request->get('sortBy')) {
            $params['order'] = $this->getFromRequest('sortBy');
        } else {
            $params['order'] = $order;
        }

        if ($this->request->get('filter')) {
            $key = $this->request->get('filterKey', 'anyField');
            $params[$key] = '%' . $this->getFromRequest('filter') . '%';
        }

        if ($this->request->get('taxonomy')) {
            $taxonomy = explode('=', $this->getFromRequest('taxonomy'));
            $params[$taxonomy[0]] = $taxonomy[1];
        }

        return $query->getContentForTwig($contentType, $params)
            ->setMaxPerPage($pageSize);
    }

    public function getFromRequestRaw(string $parameter): string
    {
        return $this->request->get($parameter, '');
    }

    public function getFromRequest(string $parameter, ?string $default = null): ?string
    {
        $parameter = trim($this->sanitiser->clean($this->request->get($parameter, '')));

        // `clean` returns a string, but we want to be able to get `null`.
        return empty($parameter) ? $default : $parameter;
    }

    public function getFromRequestArray(array $parameters, ?string $default = null): ?string
    {
        foreach ($parameters as $parameter) {
            $res = $this->getFromRequest($parameter);

            if (! empty($res)) {
                return $res;
            }
        }

        return $default;
    }

    public function validateSecret(string $secret, string $slug): bool
    {
        return $secret === $this->commonExtension->generateSecret($slug);
    }
}
