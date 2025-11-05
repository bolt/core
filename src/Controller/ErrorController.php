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
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;

class ErrorController extends SymfonyErrorController implements ErrorZoneInterface
{
    private readonly ?Request $request;

    public function __construct(
        HttpKernelInterface $httpKernel,
        private readonly Config $config,
        private readonly DetailControllerInterface $detailController,
        private readonly TemplateController $templateController,
        ErrorRendererInterface $errorRenderer,
        private readonly ParameterBagInterface $parameterBag,
        RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Security $security
    ) {
        parent::__construct($httpKernel, $this->templateController, $errorRenderer);
        $this->request = $requestStack->getParentRequest();
    }

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

        if ($code === Response::HTTP_SERVICE_UNAVAILABLE || $this->isMaintenanceEnabled($code)) {
            $twig->addGlobal('exception', $exception);

            return $this->showMaintenance();
        }

        if ($code === Response::HTTP_NOT_FOUND) {
            return $this->showNotFound();
        }

        if ($code === Response::HTTP_FORBIDDEN) {
            return $this->showForbidden();
        }

        $prod = mb_strtolower($this->parameterBag->get('kernel.environment')) === 'prod';

        if ($code === Response::HTTP_INTERNAL_SERVER_ERROR && $prod && $this->config->get('general/internal_server_error')) {
            return $this->showInternalServerError();
        }

        // If not a 404, we'll let Symfony handle it as usual.
        return parent::__invoke($exception);
    }

    private function showNotFound(): Response
    {
        foreach ($this->config->get('general/notfound') as $item) {
            $output = $this->attemptToRender($item);

            if ($output instanceof Response) {
                return $output;
            }
        }

        return new Response('404: Not found (and there was no proper page configured to display)');
    }

    private function showForbidden(): Response
    {
        if (RequestZone::isForBackend($this->request) && $this->security->isGranted('dashboard')) {
            /** @var Session $session */
            $session = $this->request->getSession();
            $session->getFlashBag()->set('danger', 'You do not have permission to access this page.');

            return new RedirectResponse($this->urlGenerator->generate('bolt_dashboard'));
        }

        foreach ($this->config->get('general/forbidden') as $item) {
            $output = $this->attemptToRender($item);

            if ($output instanceof Response) {
                return $output;
            }
        }

        return new Response('403: Forbidden (and there was no proper page configured to display)');
    }

    private function showInternalServerError(): Response
    {
        foreach ($this->config->get('general/internal_server_error') as $item) {
            $output = $this->attemptToRender($item);

            if ($output instanceof Response) {
                return $output;
            }
        }

        return new Response('500: Internal Server Error (and there was no proper page configured to display)');
    }

    private function showMaintenance(): Response
    {
        foreach ($this->config->get('general/maintenance') as $item) {
            $output = $this->attemptToRender($item);

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

    private function attemptToRender(string $item): ?Response
    {
        // First, see if it's a contenttype/slug pair:
        [$contentType, $slug] = explode('/', $item . '/');

        if (! empty($contentType) && ! empty($slug)) {
            // We wrap it in a try/catch, because we wouldn't want to
            // trigger a 404 within a 404 now, would we?
            try {
                return $this->detailController->record($slug, $contentType, false, null);
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
