<?php

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Webmozart\PathUtil\Path;


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
     * @Route("/finder/{area}/{path}", name="bolt_finder", methods={"GET"}, defaults={"path"=""}, requirements={"path"=".+"})
     */
    public function finder($area = '', $path = '')
    {
        $areas = [
            'config' => [
                'name' => "Configuration files",
                'basepath' => $this->config->path('config'),
                'show_all' => true
            ],
            'files' => [
                'name' => "Content files",
                'basepath' => $this->config->path('files'),
                'show_all' => false
            ],
            'themes' => [
                'name' => "Theme files",
                'basepath' => $this->config->path('themes'),
                'show_all' => false
            ]
        ];

        $basepath = $areas[$area]['basepath'];

        $finder = $this->findFiles($basepath, $path);

        $parent = $path ? Path::canonicalize($path . '/..') : '';

        return $this->render('finder/finder.twig', [
            'path' => $path,
            'name' => $areas[$area]['name'],
            'area' => $area,
            'finder' => $finder,
            'parent' => $parent,
            'allfiles' => $areas[$area]['show_all'] ? $this->findAllFiles($basepath) : false,
        ]);
    }

    private function findFiles($base, $path)
    {
        $fullpath = Path::canonicalize($base . '/' . $path);

        $finder = new Finder();
        $finder->in($fullpath)->depth('== 0')->sortByName(true);

        return $finder;
    }


    private function findAllFiles($base)
    {
        $fullpath = Path::canonicalize($base);

        $finder = new Finder();
        $finder->in($fullpath)->depth('< 5')->sortByName(true)->files();

        return $finder;
    }

}