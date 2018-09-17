<?php
/**
 *
 *
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\Controller;


use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
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
     * Renders a view.
     *
     * @final
     */
    protected function render(string $view, array $parameters = array(), Response $response = null): Response
    {
        $themepath = sprintf('%s/%s',
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