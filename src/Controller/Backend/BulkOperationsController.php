<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\CsrfTrait;
use Bolt\Entity\Content;
use Bolt\Event\ContentEvent;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[IsGranted(attribute: 'bulk_operations')]
class BulkOperationsController extends AbstractController implements BackendZoneInterface
{
    use CsrfTrait;

    /** @var ObjectManager */
    private $em;

    private ?Request $request;

    public function __construct(
        RequestStack $requestStack,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ManagerRegistry $managerRegistry
    ) {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function em(): ObjectManager
    {
        if ($this->em === null) {
            $this->em = $this->managerRegistry->getManager();
        }

        return $this->em;
    }

    #[Route(path: '/bulk/status/{status}', name: 'bolt_bulk_status', methods: [Request::METHOD_POST])]
    public function status(Request $request, string $status): Response
    {
        $this->validateCsrf($request, 'batch');
        $formData = $this->request?->request->getString('records') ?? '';
        $recordIds = array_map(intval(...), explode(',', $formData));

        $records = $this->findRecordsFromIds($recordIds);

        foreach ($records as $record) {
            $record->setStatus($status);
            $this->em()->persist($record);
        }

        $this->em()->flush();

        $this->addFlash('success', 'content.status_changed_successfully');
        $url = $this->request?->headers->get('referer') ?? $this->generateUrl('bolt_dashboard');

        return new RedirectResponse($url);
    }

    #[Route(path: '/bulk/delete', name: 'bolt_bulk_delete', methods: [Request::METHOD_POST])]
    public function delete(Request $request): Response
    {
        $this->validateCsrf($request, 'batch');
        $formData = $this->request?->request->getString('records') ?? '';
        $recordIds = array_map(intval(...), explode(',', $formData));

        $record = null;
        $records = $this->findRecordsFromIds($recordIds);

        foreach ($records as $record) {
            $this->em()->remove($record);
        }

        if ($record instanceof Content) {
            $event = new ContentEvent($record);
            $this->dispatcher->dispatch($event, ContentEvent::POST_DELETE);
        }

        $this->em()->flush();

        $this->addFlash('success', 'content.deleted_successfully');
        $url = $this->request?->headers->get('referer') ?? $this->generateUrl('bolt_dashboard');

        return new RedirectResponse($url);
    }

    private function findRecordsFromIds(array $ids): array
    {
        $records = [];

        foreach ($ids as $id) {
            try {
                $records[] = $this->em()->find(Content::class, $id);
            } catch (Throwable) {
            }
        }

        return $records;
    }
}
