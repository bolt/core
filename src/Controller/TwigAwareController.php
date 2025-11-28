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
use Illuminate\Support\Collection;
use Pagerfanta\PagerfantaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Environment;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

class TwigAwareController extends AbstractController
{
    protected Config $config;
    protected Environment $twig;
    protected Packages $packages;
    protected Canonical $canonical;
    protected Sanitiser $sanitiser;
    protected TemplateChooser $templateChooser;
    protected string $defaultLocale;
    private CommonExtension $commonExtension;

    #[Required]
    public function setAutowire(
        Config $config,
        Environment $twig,
        Packages $packages,
        Canonical $canonical,
        Sanitiser $sanitiser,
        TemplateChooser $templateChooser,
        string $defaultLocale,
        CommonExtension $commonExtension
    ): void {
        $this->config = $config;
        $this->twig = $twig;
        $this->packages = $packages;
        $this->canonical = $canonical;
        $this->sanitiser = $sanitiser;
        $this->templateChooser = $templateChooser;
        $this->defaultLocale = $defaultLocale;
        $this->commonExtension = $commonExtension;
    }

    /**
     * Renders a view.
     */
    public function render(string|array $template, array $parameters = [], ?Response $response = null): Response
    {
        // Make sure we have a Response
        $response ??= new Response();

        // Convert form interface to views automatically, as is done by Symfony as well
        foreach ($parameters as $k => $v) {
            if ($v instanceof FormInterface) {
                $parameters[$k] = $v->createView();
            }
        }

        // Set User in global Twig environment
        $parameters['user'] ??= $this->getUser();

        // if theme.yaml was loaded, set it as global.
        if ($this->config->has('theme')) {
            $parameters['theme'] = $this->config->get('theme');
        }

        $content = $this->renderTemplate($template, $parameters);
        $response->setContent($content);

        return $response;
    }

    /**
     * Renders a single record.
     */
    public function renderSingle(Request $request, ?Content $record, bool $requirePublished = true, array $templates = []): Response
    {
        if (! $record) {
            throw new NotFoundHttpException('Content not found');
        }

        // If the content is not 'published' we throw a 404, unless we've overridden it.
        if (($record->getStatus() !== Statuses::PUBLISHED) && $requirePublished) {
            throw new NotFoundHttpException('Content is not published');
        }

        if (! $recordDefinition = $record->getDefinition()) {
            throw new NotFoundHttpException('Content definition could not be found');
        }

        // If the ContentType is 'viewless' we also throw a 404.
        if (($recordDefinition->get('viewless') === true) && $requirePublished) {
            throw new NotFoundHttpException('Content is not viewable');
        }

        // If the locale is the wrong locale
        if (! $this->validLocaleForContentType($request, $recordDefinition)) {
            return $this->redirectToDefaultLocale($request);
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

    protected function validLocaleForContentType(Request $request, ContentType $contentType): bool
    {
        if ($contentType->isKeyNotEmpty('locales')) {
            return $contentType->get('locales')->contains($request->getLocale());
        }

        return $request->getLocale() === $this->defaultLocale;
    }

    protected function redirectToDefaultLocale(Request $request): ?Response
    {
        $request->getSession()->set('_locale', $this->defaultLocale);

        $params = $request->attributes->get('_route_params');

        if (isset($params['_locale'])) {
            $params['_locale'] = $this->defaultLocale;
        }

        return $this->redirectToRoute($request->get('_route'), $params);
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
     *
     * @param string|array<null|string|TemplateselectField> $template
     */
    public function renderTemplate(string|array $template, array $parameters = []): string
    {
        $this->setThemePackage();
        $this->setTwigLoader();

        // Resolve string|array of templates into the first one that is found.
        if (is_array($template)) {
            $templates = (new Collection($template))
                ->map(function (null|string|TemplateselectField $element): ?string {
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

    /**
     * @return PagerfantaInterface<Content>
     */
    public function createPager(Request $request, Query $query, string $contentType, int $pageSize, string $order): PagerfantaInterface
    {
        return $query
            ->getContentForTwig($contentType, $this->createPagerParams($request, $order))
            ->setMaxPerPage($pageSize);
    }

    public function createPagerParams(Request $request, string $order): array
    {
        $params = [
            'status' => '!unknown',
            'returnmultiple' => true,
        ];

        if ($request->get('sortBy')) {
            $params['order'] = $this->getFromRequest($request, 'sortBy');
        } else {
            $params['order'] = $order;
        }

        if ($request->get('filter')) {
            $key = $request->get('filterKey', 'anyField');
            $params[$key] = '%' . $this->getFromRequest($request, 'filter') . '%';
        }

        if ($request->get('taxonomy')) {
            $taxonomy = explode('=', (string) $this->getFromRequest($request, 'taxonomy'));
            $params[$taxonomy[0]] = $taxonomy[1];
        }

        return $params;
    }

    public function getFromRequestRaw(Request $request, string $parameter): string
    {
        return $request->get($parameter) ?? '';
    }

    public function getFromRequest(Request $request, string $parameter, ?string $default = null): ?string
    {
        $parameter = mb_trim($this->sanitiser->clean($request->get($parameter, '') ?? ''));

        // `clean` returns a string, but we want to be able to get `null`.
        return empty($parameter) ? $default : $parameter;
    }

    public function getFromRequestArray(Request $request, array $parameters, ?string $default = null): ?string
    {
        foreach ($parameters as $parameter) {
            $res = $this->getFromRequest($request, $parameter);

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
