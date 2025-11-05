<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Repository\LogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(attribute: 'systemlog')]
class LogViewerController extends TwigAwareController implements BackendZoneInterface
{
    #[Route(path: '/logviewer', name: 'bolt_logviewer', methods: [Request::METHOD_GET])]
    public function index(LogRepository $log): Response
    {
        $amount = $this->config->get('general/log/amount', 10);
        $page = (int) $this->getFromRequest('page', '1');

        $items = $log->findLatest($page, $amount);

        return $this->render('@bolt/pages/logviewer.html.twig', [
            'items' => $items,
        ]);
    }
}
