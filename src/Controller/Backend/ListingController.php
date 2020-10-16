<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Storage\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ListingController extends TwigAwareController implements BackendZoneInterface
{
    /**
     * @Route("/content/{contentType}", name="bolt_content_overview")
     */
    public function overview(Query $query, string $contentType = ''): Response
    {
        $contentTypeObject = ContentType::factory($contentType, $this->config->get('contenttypes'));

        $page = (int) $this->getFromRequest('page', '1');

        $pager = $this->createPager($query, $contentType, $contentTypeObject->get('records_per_page'), $contentTypeObject->get('order'));

        $nbPages = $pager->getNbPages();

        if ($page > $nbPages) {
            return $this->redirectToRoute('bolt_content_overview', [
                'contentType' => $contentType,
                'page' => $nbPages,
            ]);
        }

        $records = $pager->setCurrentPage($page);

        if ($contentTypeObject->get('singleton', false) === true) {
            if ($records->getNbResults() === 0) {
                // No such CT yet. Create new.
                return $this->redirectToRoute('bolt_content_new', ['contentType' => $contentType]);
            }
            // Redirect to the record
            /** @var Content $record */
            $record = current((array) $records->getCurrentPageResults());

            return $this->redirectToRoute('bolt_content_edit', ['id' => $record->getId()]);
        }

        return $this->render('@bolt/content/listing.html.twig', [
            'contentType' => $contentTypeObject,
            'records' => $records,
            'sortBy' => $this->getFromRequest('sortBy'),
            'filterValue' => $this->getFromRequest('filter'),
            'filterKey' => $this->getFromRequest('filterKey'),
        ]);
    }
}
