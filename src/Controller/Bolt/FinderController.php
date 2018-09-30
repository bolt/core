<?php

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Class EditRecordController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class FinderController extends AbstractController
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/finder/{path}", name="bolt_finder", methods={"GET"})
     */
    public function finder($area = '')
    {
        dump($area);

        $path = $this->config->path('config');

        $files = $this->findFiles($path);

        return $this->render('finder/finder.twig', [
            'path' => $path,
            'area' => $area,
            'files' => $files,
        ]);
    }

    private function findFiles($path)
    {
        $finder = new Finder();
        $finder->files()->in($path);

        return $finder;
    }
    
}