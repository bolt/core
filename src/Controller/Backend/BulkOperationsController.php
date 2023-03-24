<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\CsrfTrait;
use Bolt\Entity\Content;
use Bolt\Event\ContentEvent;
use Doctrine\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @Security("is_granted('bulk_operations')")
 */
class BulkOperationsController extends AbstractController implements BackendZoneInterface
{
    use CsrfTrait;

    /** @var ObjectManager */
    private $em;

    /** @var Request */
    private $request;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(RequestStack $requestStack, EventDispatcherInterface $dispatcher)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->dispatcher = $dispatcher;
    }

    public function em(): ObjectManager
    {
        if ($this->em === null) {
            $this->em = $this->getDoctrine()->getManager();
        }

        return $this->em;
    }

    /**
     * @Route("/bulk/status/{status}", name="bolt_bulk_status", methods={"POST"})
     */
    public function status(string $status): Response
    {
        $this->validateCsrf('batch');
        $formData = $this->request->request->get('records');
        $recordIds = array_map('intval', explode(',', $formData));

        $records = $this->findRecordsFromIds($recordIds);

        foreach ($records as $record) {
            $record->setStatus($status);
            $this->em()->persist($record);
        }

        $this->em()->flush();

        $this->addFlash('success', 'content.status_changed_successfully');
        $url = $this->request->headers->get('referer');

        return new RedirectResponse($url);
    }

    /**
     * @Route("/bulk/delete", name="bolt_bulk_delete", methods={"POST"})
     */
    public function delete(): Response
    {
        $this->validateCsrf('batch');
        $formData = $this->request->request->get('records');
        $recordIds = array_map('intval', explode(',', $formData));

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
        $url = $this->request->headers->get('referer');

        return new RedirectResponse($url);
    }

    private function findRecordsFromIds(array $ids): array
    {
        $records = [];

        foreach ($ids as $id) {
            try {
                $records[] = $this->em()->find(Content::class, $id);
            } catch (\Throwable $e) {
            }
        }

        return $records;
    }
}
