<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use Bolt\Controller\Frontend\DetailController;
use Bolt\Controller\Frontend\TemplateController;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as SymfonyExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;

class ExceptionController extends SymfonyExceptionController
{
    /** @var Config */
    private $config;

    /** @var DetailController */
    private $detailController;

    /** @var TemplateController */
    private $templateController;

    public function __construct(Environment $twig, bool $debug, Config $config, DetailController $detailController, TemplateController $templateController)
    {
        $this->config = $config;
        $this->detailController = $detailController;
        $this->templateController = $templateController;

        parent::__construct($twig, $debug);
    }

    /**
     * Show an exception. Mainly used for custom 404 pages, otherwise falls back
     * to Symfony's error handling
     */
    public function showAction(Request $request, FlattenException $exception, ?DebugLoggerInterface $logger = null): Response
    {
        $code = $exception->getStatusCode();

        if ($code === 404) {
            $this->twig->addGlobal('exception', $exception);

            return $this->showNotFound();
        }

        // If not a 404, we'll let Symfony handle it as usual.
        return parent::showAction($request, $exception, $logger);
    }

    private function showNotFound()
    {
        foreach ($this->config->get('general/notfound') as $item) {
            $output = $this->attemptToRender($item);

            if ($output instanceof Response) {
                return $output;
            }
        }

        return new Response('Oh no');
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
