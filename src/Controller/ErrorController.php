<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use Bolt\Controller\Frontend\DetailControllerInterface;
use Bolt\Controller\Frontend\TemplateController;
use Bolt\Widget\Injector\RequestZone;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\ErrorController as SymfonyErrorController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;

class ErrorController extends SymfonyErrorController implements ErrorZoneInterface
{
    public function __construct(
        HttpKernelInterface $httpKernel,
        private readonly Config $config,
        private readonly DetailControllerInterface $detailController,
        private readonly TemplateController $templateController,
        ErrorRendererInterface $errorRenderer,
        private readonly ParameterBagInterface $parameterBag,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly LocaleSwitcher $localeSwitcher,
        string $locales,
    ) {
        parent::__construct($httpKernel, $this->templateController, $errorRenderer);

        $this->localeCodes = explode('|', $locales);
    }

    /** @var list<string> */
    private readonly array $localeCodes;

    /**
     * Show an exception. Mainly used for custom 404 pages, otherwise falls back
     * to Symfony's error handling
     */
    public function showAction(Environment $twig, Throwable $exception): Response
    {
        if (method_exists($exception, 'getStatusCode')) {
            $code = $exception->getStatusCode();
        } else {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        try {
            $twig->addGlobal('exception', $exception);
        } catch (LogicException) {
            // Fine! We'll just _not_ add the exception to the global scope!
        }

        // We need the parent request here, but fall back to current if not found
        if ($request = $this->requestStack->getParentRequest() ?? $this->requestStack->getCurrentRequest()) {
            $this->setLocaleFromPath($request);

            if ($code === Response::HTTP_SERVICE_UNAVAILABLE || $this->isMaintenanceEnabled($code)) {
                $twig->addGlobal('exception', $exception);

                return $this->showMaintenance($request);
            }

            if ($code === Response::HTTP_NOT_FOUND) {
                return $this->showNotFound($request);
            }

            if ($code === Response::HTTP_FORBIDDEN) {
                return $this->showForbidden($request);
            }

            $prod = mb_strtolower($this->parameterBag->get('kernel.environment')) === 'prod';

            if ($code === Response::HTTP_INTERNAL_SERVER_ERROR && $prod && $this->config->get('general/internal_server_error')) {
                return $this->showInternalServerError($request);
            }
        }

        // If not a 404, we'll let Symfony handle it as usual.
        return parent::__invoke($exception);
    }

    private function showNotFound(Request $request): Response
    {
        foreach ($this->config->get('general/notfound') as $item) {
            $output = $this->attemptToRender($request, $item);

            if ($output instanceof Response) {
                return $output;
            }
        }

        return new Response('404: Not found (and there was no proper page configured to display)');
    }

    private function showForbidden(Request $request): Response
    {
        if (RequestZone::isForBackend($request) && $this->security->isGranted('dashboard')) {
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->set('danger', 'You do not have permission to access this page.');

            return new RedirectResponse($this->urlGenerator->generate('bolt_dashboard'));
        }

        foreach ($this->config->get('general/forbidden') as $item) {
            $output = $this->attemptToRender($request, $item);

            if ($output instanceof Response) {
                return $output;
            }
        }

        return new Response('403: Forbidden (and there was no proper page configured to display)');
    }

    private function showInternalServerError(Request $request): Response
    {
        foreach ($this->config->get('general/internal_server_error') as $item) {
            $output = $this->attemptToRender($request, $item);

            if ($output instanceof Response) {
                return $output;
            }
        }

        return new Response('500: Internal Server Error (and there was no proper page configured to display)');
    }

    private function showMaintenance(Request $request): Response
    {
        foreach ($this->config->get('general/maintenance') as $item) {
            $output = $this->attemptToRender($request, $item);

            if ($output instanceof Response) {
                return $output;
            }
        }

        return new Response('503: Maintenance mode (and there was no proper page configured to display)');
    }

    private function isMaintenanceEnabled(int $code): bool
    {
        // Only applies to NOT_FOUND and FORBIDDEN in frontend
        if (! in_array($code, [Response::HTTP_NOT_FOUND, Response::HTTP_FORBIDDEN], true)) {
            return false;
        }

        return filter_var($this->config->get('general/maintenance_mode', false), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Recovers the locale from the first path segment (e.g. `/de/...` => `de`),
     * which Symfony's `LocaleListener` never did because no route matched.
     *
     * Applied to the given request and to the current one (a sub-request when an
     * error page is rendered, which Twig's `app.request` resolves to). The
     * `LocaleSwitcher` then syncs the translator and the router's `RequestContext`,
     * so `{% trans %}` strings and `path()` calls follow suit.
     */
    private function setLocaleFromPath(Request $request): void
    {
        // Only derive the locale from the path when routing didn't run. With a
        // matched route the locale is already set, and the first segment is a
        // ContentType slug rather than a locale: a ContentType `it` isn't Italian.
        if (! $request->attributes->has('_route')) {
            // Cast: `mb_trim()` is analysed as `string|false`, but `getPathInfo()`
            // always returns a string.
            $segment = explode('/', (string) mb_trim($request->getPathInfo(), '/'))[0];

            if ($segment !== '' && in_array($segment, $this->localeCodes, true)) {
                $request->setLocale($segment);
            }
        }

        // The error sub-request is a fresh Request that never saw the URL's locale,
        // so propagate it regardless of how the locale was determined.
        $currentRequest = $this->requestStack->getCurrentRequest();
        if ($currentRequest instanceof Request && $currentRequest !== $request) {
            $currentRequest->setLocale($request->getLocale());
        }

        $this->localeSwitcher->setLocale($request->getLocale());
    }

    private function attemptToRender(Request $request, string $item): ?Response
    {
        // First, see if it's a contenttype/slug pair:
        [$contentType, $slug] = explode('/', $item . '/');

        if (! empty($contentType) && ! empty($slug)) {
            // We wrap it in a try/catch, because we wouldn't want to
            // trigger a 404 within a 404 now, would we?
            try {
                // Pass the locale explicitly, or the record falls back to the default.
                return $this->detailController->record($request, $slug, $contentType, false, $request->getLocale());
            } catch (NotFoundHttpException) {
                // Just continue to the next one.
            }
        }

        // Then, let's see if it's a template we can render.
        try {
            return $this->templateController->template($item);
        } catch (LoaderError) {
            // Just continue to the next one.
        }

        return null;
    }
}
