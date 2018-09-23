<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Config;
use Bolt\Repository\ContentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContentListingController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ContentListingController extends AbstractController
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/content", name="bolt_contentlisting")
     *
     * @param null|string $vueRouting
     *
     * @return Response
     */
    public function listing(ContentRepository $content, Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);

        /** @var Content $records */
        $records = $content->findAll($page);

        return $this->render('bolt/content/listing.twig', ['records' => $records]);

    }
}
