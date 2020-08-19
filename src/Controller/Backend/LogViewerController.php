<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Log;
use Bolt\Repository\LogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class LogViewerController extends TwigAwareController implements BackendZoneInterface
{
    /**
     * @Route("/logviewer", name="bolt_logviewer", methods={"GET"})
     */
    public function index(LogRepository $log): Response
    {
        $amount = $this->config->get('general/log/amount', 10);
        $page = (int) $this->getFromRequest('page', '1');

        /** @var Log $items */
        $items = $log->findLatest($page, $amount);

        return $this->render('@bolt/pages/logviewer.html.twig', [
            'items' => $items,
        ]);
    }
}
