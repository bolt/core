<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\TwigAwareController;
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

        return $this->render('@bolt/content/listing.html.twig', [
            'contentType' => $contentTypeObject,
            'records' => $records,
            'sortBy' => $this->getFromRequest('sortBy'),
            'filterValue' => $this->getFromRequest('filter'),
        ]);
    }
}
