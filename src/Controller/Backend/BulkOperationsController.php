<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\CsrfTrait;
use Bolt\Entity\Content;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class BulkOperationsController extends AbstractController implements BackendZone
{
    use CsrfTrait;

    private $em = null;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function em()
    {
        if ($this->em === null) {
            $this->em = $this->getDoctrine()->getManager();
        }

        return $this->em;
    }

    /**
     * @Route("/bulk/status/{status}", name="bolt_bulk_status", methods={"POST"})
     */
    public function status(Request $request, string $status): Response
    {
        $this->validateCsrf($request, 'batch');
        $formData = $request->request->get('records');
        $recordIds = array_map('intval', explode(',', $formData));

        $records = $this->findRecordsFromIds($recordIds);

        foreach ($records as $record) {
            $record->setStatus($status);
            $this->em()->persist($record);
        }

        $this->em()->flush();

        $this->addFlash('success', 'content.status_changed_successfully');
        $url = $request->headers->get('referer');
        return new RedirectResponse($url);
    }

    /**
     * @Route("/bulk/delete", name="bolt_bulk_delete", methods={"POST"})
     */
    public function delete(Request $request): Response
    {
        $this->validateCsrf($request, 'batch');
        $formData = $request->request->get('records');
        $recordIds = array_map('intval', explode(',', $formData));

        $records = $this->findRecordsFromIds($recordIds);

        foreach ($records as $record) {
            $this->em()->remove($record);
        }

        $this->em()->flush();

        $this->addFlash('success', 'content.deleted_successfully');
        $url = $request->headers->get('referer');
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
