<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Content\ContentTypeFactory;
use Bolt\Controller\BaseController;
use Bolt\Repository\ContentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContentListingController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ContentListingController extends BaseController
{
    /**
     * @Route("/content/{contenttype}", name="bolt_contentlisting")
     *
     * @param ContentRepository $content
     * @param Request           $request
     * @param string            $contenttype
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function listing(ContentRepository $content, Request $request, string $contenttype = ''): Response
    {
        $contenttype = ContentTypeFactory::get($contenttype, $this->config->get('contenttypes'));

        $page = (int) $request->query->get('page', 1);

        /** @var Content $records */
        $records = $content->findAll($page, $contenttype);

        return $this->renderTemplate('content/listing.twig', [
            'records' => $records,
            'contenttype' => $contenttype,
        ]);
    }
}
