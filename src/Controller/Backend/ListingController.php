<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Security\ContentVoter;
use Bolt\Storage\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListingController extends TwigAwareController implements BackendZoneInterface
{
    #[Route(path: '/content/{contentType}', name: 'bolt_content_overview')]
    public function overview(Request $request, Query $query, string $contentType = ''): Response
    {
        $contentTypeObject = ContentType::factory($contentType, $this->config->get('contenttypes'));

        $this->denyAccessUnlessGranted(ContentVoter::CONTENT_MENU_LISTING, $contentTypeObject);

        $page = (int) $this->getFromRequest($request, 'page', '1');

        $pager = $this->createPager($request, $query, $contentType, $contentTypeObject->get('records_per_page'), $contentTypeObject->get('order'));

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

        [$taxonomyName, $taxonomyValue] = explode('=', $this->getFromRequest($request, 'taxonomy', '') . '=');

        return $this->render('@bolt/content/listing.html.twig', [
            'contentType' => $contentTypeObject,
            'records' => $records,
            'sortBy' => $this->getFromRequest($request, 'sortBy'),
            'filterValue' => $this->getFromRequest($request, 'filter'),
            'taxonomyName' => $taxonomyName,
            'taxonomyValue' => $taxonomyValue,
            'filterKey' => $this->getFromRequest($request, 'filterKey'),
        ]);
    }
}
