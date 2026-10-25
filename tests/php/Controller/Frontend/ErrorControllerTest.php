<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller\Frontend;

use Bolt\Configuration\Config;
use Bolt\Controller\ErrorController;
use Bolt\Entity\Content;
use Bolt\Enum\Statuses;
use Bolt\Tests\DbAwareTestCase;
use Bolt\Widget\Injector\RequestZone;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
use Twig\Environment;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

class ErrorControllerTest extends DbAwareTestCase
{
    private const HEADING_EN = 'Not-found-heading-in-English';
    private const HEADING_NL = 'Niet-gevonden-kop-in-het-Nederlands';

    protected function setUp(): void
    {
        // Without a `canonical`, the CanonicalSubscriber chokes on frontend requests.
        putenv('BOLT_CANONICAL=http://localhost');
        $_SERVER['BOLT_CANONICAL'] = 'http://localhost';
        $_ENV['BOLT_CANONICAL'] = 'http://localhost';

        parent::setUp();
    }

    protected function tearDown(): void
    {
        putenv('BOLT_CANONICAL');
        unset($_SERVER['BOLT_CANONICAL'], $_ENV['BOLT_CANONICAL']);

        parent::tearDown();
    }

    public function testNotFoundRecordRendersLocalizedFieldInRequestedLocale(): void
    {
        $this->seedLocalizedNotFoundPage();

        $this->client->request('GET', '/nl/this-page-does-not-exist');
        $response = $this->client->getResponse();
        $body = (string) $response->getContent();

        self::assertSame(404, $response->getStatusCode());
        self::assertStringContainsString('<html lang="nl"', $body);
        self::assertStringContainsString(self::HEADING_NL, $body);
        self::assertStringNotContainsString(self::HEADING_EN, $body);
    }

    public function testRoutedNotFoundRendersLocalizedFieldInRequestedLocale(): void
    {
        // A 404 thrown *after* routing matched: the error sub-request is a fresh
        // request that Bolt's LocaleSubscriber forces to the default locale.
        $this->seedLocalizedNotFoundPage();

        $this->client->request('GET', '/nl/pages/this-record-does-not-exist');
        $response = $this->client->getResponse();
        $body = (string) $response->getContent();

        self::assertSame(404, $response->getStatusCode());
        self::assertStringContainsString('<html lang="nl"', $body);
        self::assertStringContainsString(self::HEADING_NL, $body);
    }

    public function testNotFoundRecordRendersLocalizedFieldInDefaultLocale(): void
    {
        $this->seedLocalizedNotFoundPage();

        $this->client->request('GET', '/this-page-does-not-exist');
        $response = $this->client->getResponse();
        $body = (string) $response->getContent();

        self::assertSame(404, $response->getStatusCode());
        self::assertStringContainsString('<html lang="en"', $body);
        self::assertStringContainsString(self::HEADING_EN, $body);
        self::assertStringNotContainsString(self::HEADING_NL, $body);
    }

    public function testForbiddenRecordRendersLocalizedFieldInRequestedLocale(): void
    {
        $page = $this->seedLocalizedPage();
        $this->setGeneralConfig('forbidden', ['page/' . $page->getId()]);

        // A frontend 403 isn't reachable from a routed request here: every
        // `access_control` rule is backend, and the backend 403 redirects to the
        // dashboard. So drive the error controller directly, as the kernel does.
        // It returns a plain 200 - promoting the status is the kernel's job - so we
        // assert only the locale handling that is this controller's responsibility.
        $response = $this->renderError(new AccessDeniedHttpException(), '/nl/this-page-is-forbidden');
        $body = (string) $response->getContent();

        self::assertStringContainsString('<html lang="nl"', $body);
        // Only `general/forbidden` points at this record, so its presence also
        // proves the forbidden page (not the 404 page) rendered.
        self::assertStringContainsString(self::HEADING_NL, $body);
        self::assertStringNotContainsString(self::HEADING_EN, $body);
    }

    public function testErrorPageRecoversTranslatorLocaleFromPath(): void
    {
        // No route matched, so LocaleAwareListener never set the translator locale.
        $page = $this->seedLocalizedPage();
        $this->setGeneralConfig('notfound', ['page/' . $page->getId()]);

        $this->renderError(new NotFoundHttpException(), '/nl/this-page-does-not-exist');

        /** @var TranslatorInterface $translator */
        $translator = self::getContainer()->get('translator');
        self::assertSame('nl', $translator->getLocale());
    }

    public function testErrorPageRecoversRouterContextLocaleFromPath(): void
    {
        // Without this, `path()` in a Dutch-rendered error page emits `/en/...`.
        $this->registerFixtureTemplatePath();
        $this->setGeneralConfig('notfound', ['locale_probe.html.twig']);

        $this->renderError(new NotFoundHttpException(), '/nl/this-page-does-not-exist');

        /** @var RouterInterface $router */
        $router = self::getContainer()->get('router');
        self::assertSame('nl', $router->getContext()->getParameter('_locale'));
    }

    public function testErrorPageKeepsLocaleWhenRouteMatched(): void
    {
        // With a matched route the locale is already set, and `it` is a ContentType
        // slug rather than Italian. Recovering it from the path would be wrong.
        $this->registerFixtureTemplatePath();
        $this->setGeneralConfig('notfound', ['locale_probe.html.twig']);

        $body = (string) $this->renderError(new NotFoundHttpException(), '/it/no-such-record', 'listing')->getContent();

        self::assertStringContainsString('LOCALE_PROBE:Error 404', $body);
        self::assertStringNotContainsString('Errore 404', $body);
    }

    public function testErrorPageTranslatesUnderscoreFunctionInRequestedLocale(): void
    {
        // End-to-end: a `{{ __('...') }}` string in the rendered error page must be
        // translated in the locale recovered from the URL. `http_error.name` is
        // "Error %status_code%" (en) / "Fout %status_code%" (nl).
        $this->registerFixtureTemplatePath();
        $this->setGeneralConfig('notfound', ['locale_probe.html.twig']);

        $body = (string) $this->renderError(new NotFoundHttpException(), '/nl/this-page-does-not-exist')->getContent();

        self::assertStringContainsString('LOCALE_PROBE:Fout 404', $body);
        self::assertStringNotContainsString('Error 404', $body);
    }

    public function testErrorPageTranslatesUnderscoreFunctionInDefaultLocale(): void
    {
        // Counterpart of the test above: no locale segment, so `en`.
        $this->registerFixtureTemplatePath();
        $this->setGeneralConfig('notfound', ['locale_probe.html.twig']);

        $body = (string) $this->renderError(new NotFoundHttpException(), '/this-page-does-not-exist')->getContent();

        self::assertStringContainsString('LOCALE_PROBE:Error 404', $body);
        self::assertStringNotContainsString('Fout 404', $body);
    }

    public function testNotFoundPageWithNonLocalizedContentTypeDoesNotError(): void
    {
        // The default `notfound` is `blocks/404-not-found`, and `blocks` isn't
        // localized - so `nl` sends it down the "wrong locale" path, which has no
        // route to redirect to here. It must render the record rather than error.
        $this->client->request('GET', '/nl/this-page-does-not-exist');
        $response = $this->client->getResponse();

        self::assertSame(404, $response->getStatusCode());
        self::assertStringContainsString('404 Page not found', (string) $response->getContent());
    }

    /** Point the 404 page at a record with a per-locale `heading`. */
    private function seedLocalizedNotFoundPage(): void
    {
        $page = $this->seedLocalizedPage();

        $this->setGeneralConfig('notfound', ['page/' . $page->getId()]);
    }

    /** Give a published `pages` record distinct `heading` values per locale. */
    private function seedLocalizedPage(): Content
    {
        $page = $this->getPublishedPage();

        $page->setFieldValue('heading', self::HEADING_EN, 'en');
        $page->setFieldValue('heading', self::HEADING_NL, 'nl');
        $page->getField('heading')->mergeNewTranslations();
        $this->getEm()->flush();

        return $page;
    }

    /**
     * Invoke the configured `error_controller` directly, the way the kernel does,
     * with a frontend request for the given path on the stack.
     *
     * Pass `$route` to simulate an error raised *after* routing matched.
     */
    private function renderError(Throwable $exception, string $path, ?string $route = null): Response
    {
        $request = Request::create('http://localhost' . $path);
        RequestZone::setToRequest($request, RequestZone::FRONTEND);

        if ($route !== null) {
            $request->attributes->set('_route', $route);
        }

        /** @var RequestStack $requestStack */
        $requestStack = self::getContainer()->get(RequestStack::class);
        $requestStack->push($request);

        try {
            /** @var ErrorController $errorController */
            $errorController = self::getContainer()->get(ErrorController::class);
            /** @var Environment $twig */
            $twig = self::getContainer()->get('twig');

            return $errorController->showAction($twig, $exception);
        } finally {
            $requestStack->pop();
        }
    }

    /**
     * Make `Fixtures/` resolvable by name, so a fixture template can be used as the
     * `notfound` config. Adds to the existing loader rather than replacing it, the
     * way TwigAwareController::setTwigLoader() does, so the path survives rendering.
     */
    private function registerFixtureTemplatePath(): void
    {
        /** @var Environment $twig */
        $twig = self::getContainer()->get('twig');

        $loader = $twig->getLoader();
        $loaders = $loader instanceof ChainLoader ? $loader->getLoaders() : [$loader];

        foreach ($loaders as $candidate) {
            if ($candidate instanceof FilesystemLoader) {
                $candidate->addPath(__DIR__ . '/Fixtures');

                return;
            }
        }

        self::fail('Could not find a FilesystemLoader to register the fixture template path on.');
    }

    private function getPublishedPage(): Content
    {
        $page = $this->getEm()->getRepository(Content::class)
            ->findOneBy(['contentType' => 'pages', 'status' => Statuses::PUBLISHED]);

        self::assertInstanceOf(Content::class, $page, 'Expected a published "pages" record in the fixtures.');

        return $page;
    }

    /**
     * Override a `general/*` configuration value at runtime.
     *
     * @param array<string> $value
     */
    private function setGeneralConfig(string $key, array $value): void
    {
        $config = self::getContainer()->get(Config::class);

        // Mutate only the `general` collection in place; rebuilding the whole data
        // tree would turn `contenttypes` into plain collections and break typing.
        $property = new \ReflectionProperty(Config::class, 'data');
        $property->getValue($config)->get('general')->put($key, $value);
    }
}
