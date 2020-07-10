<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use Bolt\Controller\Frontend\DetailController;
use Bolt\Controller\Frontend\TemplateController;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ErrorController as SymfonyErrorController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Twig\Environment;
use Twig\Error\LoaderError;

class ErrorController extends SymfonyErrorController
{
    /** @var Config */
    private $config;

    /** @var TemplateController */
    private $templateController;

    /** @var DetailController */
    private $detailController;

    public function __construct(HttpKernelInterface $httpKernel, Config $config, DetailController $detailController, TemplateController $templateController, ErrorRendererInterface $errorRenderer)
    {
        $this->config = $config;
        $this->templateController = $templateController;

        parent::__construct($httpKernel, $templateController, $errorRenderer);
        $this->detailController = $detailController;
    }

    /**
     * Show an exception. Mainly used for custom 404 pages, otherwise falls back
     * to Symfony's error handling
     */
    public function showAction(Environment $twig, \Throwable $exception): Response
    {
        if (method_exists($exception, 'getStatusCode')) {
            $code = $exception->getStatusCode();
        } else {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        if ($code === Response::HTTP_NOT_FOUND) {
            $twig->addGlobal('exception', $exception);

            // If Maintenance is on, show that, instead of the 404.
            if ($this->isMaintenanceEnabled()) {
                return $this->showMaintenance();
            }

            return $this->showNotFound();
        }

        if ($code === Response::HTTP_SERVICE_UNAVAILABLE) {
            $twig->addGlobal('exception', $exception);

            return $this->showMaintenance();
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

    private function isMaintenanceEnabled()
    {
        return $this->config->get('general/maintenance_mode', false);
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

    private function attemptToRender(string $item): ?Response
    {
        // First, see if it's a contenttype/slug pair:
        [$contentType, $slug] = explode('/', $item . '/');

        if (! empty($contentType) && ! empty($slug)) {
            // We wrap it in a try/catch, because we wouldn't want to
            // trigger a 404 within a 404 now, would we?
            try {
                return $this->detailController->record($slug, $contentType, false);
            } catch (NotFoundHttpException $e) {
                // Just continue to the next one.
            }
        }

        // Then, let's see if it's a template we can render.
        try {
            return $this->templateController->template($item);
        } catch (LoaderError $e) {
            // Just continue to the next one.
        }

        return null;
    }
}
