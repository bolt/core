<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/content", methods={"GET"}, name="listing")
     */
    public function contentListing(ContentRepository $content, Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);

        /** @var Content $records */
        $records = $content->findLatest($page);

        return $this->render('listing.twig', ['records' => $records]);
    }

    /**
     * @Route("/record/{id<[1-9]\d*>}", methods={"GET"}, name="record_by_id")
     * @Route("/record/{slug<[a-z0-9_-]+>}", methods={"GET"}, name="record")
     *
     * @param ContentRepository $contentRepository
     * @param FieldRepository   $fieldRepository
     * @param null              $id
     * @param null              $slug
     *
     * @return Response
     */
    public function record(ContentRepository $contentRepository, FieldRepository $fieldRepository, $id = null, $slug = null): Response
    {
        if ($id) {
            $record = $contentRepository->findOneBy(['id' => $id]);
        } elseif ($slug) {
            $field = $fieldRepository->findOneBySlug($slug);
            $record = $field->getContent();
//            $record = $contentRepository->findOneBySlug($slug);
        }

        $recordSlug = $record->getDefinition()->singular_slug;

        $context = [
            'record' => $record,
            $recordSlug => $record,
        ];

        return $this->render('record.twig', $context);
    }

    /**
     * Renders a view.
     *
     * @final
     *
     * @param string        $view
     * @param array         $parameters
     * @param Response|null $response
     *
     * @return Response
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $themepath = sprintf(
            '%s/%s',
            $this->config->path('themes'),
            $this->config->get('general/theme')
        );

        $twig = $this->container->get('twig');

        $loader = $twig->getLoader();
        $loader->addPath($themepath);
        $twig->setLoader($loader);

        $parameters['config'] = $this->config;

        $content = $twig->render($view, $parameters);

        if ($response === null) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }
}
