<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Common\Str;
use Bolt\Configuration\Areas;
use Bolt\Configuration\Config;
use Bolt\Controller\BaseController;
use Bolt\Repository\MediaRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\PathUtil\Path;

/**
 * Class EditRecordController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class FinderController extends BaseController
{
    /** @var Areas */
    private $areas;

    public function __construct(Config $config, Areas $areas)
    {
        $this->config = $config;
        $this->areas = $areas;
    }

    /**
     * @Route("/finder/{area}", name="bolt_finder", methods={"GET"})
     *
     * @param $area
     * @param Request         $request
     * @param MediaRepository $mediaRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function finder($area, Request $request, MediaRepository $mediaRepository)
    {
        $path = $request->query->get('path');
        if (!str::endsWith($path, '/')) {
            $path .= '/';
        }

        $area = $this->areas->get($area);

        $finder = $this->findFiles($area->get('basepath'), $path);

        $media = $mediaRepository->findAll();

        $parent = $path !== '/' ? Path::canonicalize($path . '/..') : '';

        return $this->renderTemplate('finder/finder.twig', [
            'path' => $path,
            'name' => $area->get('name'),
            'area' => $area->get('key'),
            'finder' => $finder,
            'parent' => $parent,
            'media' => $media,
            'allfiles' => $area->get('show_all') ? $this->buildIndex($area->get('basepath')) : false,
        ]);
    }

    private function findFiles($base, $path)
    {
        $fullpath = Path::canonicalize($base . '/' . $path);

        $finder = new Finder();
        $finder->in($fullpath)->depth('== 0')->sortByName(true);

        return $finder;
    }

    private function buildIndex($base)
    {
        $fullpath = Path::canonicalize($base);

        $finder = new Finder();
        $finder->in($fullpath)->depth('< 5')->sortByName(true)->files();

        $index = [];

        foreach ($finder as $file) {
            $index[] = [
                'filename' => $file->getRelativePathname(),
                'description' => '',
            ];
        }

        return $index;
    }
}
