<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\TwigAwareController;
use Bolt\Storage\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ContentOverviewController extends TwigAwareController implements BackendZoneInterface
{
    /**
     * @Route("/content/{contentType}", name="bolt_content_overview")
     */
    public function overview(Request $request, Query $query, string $contentType = ''): Response
    {
        $contentTypeObject = ContentType::factory($contentType, $this->config->get('contenttypes'));

        $params = [
            'status' => '!unknown',
        ];

        if ($request->get('sortBy')) {
            $params['order'] = $request->get('sortBy');
        }

        if ($request->get('filter')) {
            $params['anyField'] = '%' . $request->get('filter') . '%';
        }

        if ($request->get('taxonomy')) {
            $taxonomy = explode('=', $request->get('taxonomy'));
            $params[$taxonomy[0]] = $taxonomy[1];
        }

        $records = $query->getContentForTwig($contentType, $params)
            ->setMaxPerPage($contentTypeObject->get('records_per_page'));

        return $this->renderTemplate('@bolt/content/listing.html.twig', [
            'contentType' => $contentTypeObject,
            'records' => $records,
            'sortBy' => $request->get('sortBy'),
            'filterValue' => $request->get('filter'),
        ]);
    }
}
