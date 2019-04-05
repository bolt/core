<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Str;
use Bolt\Configuration\Areas;
use Bolt\Controller\TwigAwareController;
use Bolt\Repository\MediaRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\PathUtil\Path;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class FilemanagerController extends TwigAwareController
{
    /**
     * @var Areas
     */
    private $areas;

    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    public function __construct(Areas $areas, MediaRepository $mediaRepository)
    {
        $this->areas = $areas;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @Route("/filemanager/{area}", name="bolt_filemanager", methods={"GET"})
     */
    public function filemanager(string $area, Request $request): Response
    {
        $path = $request->query->get('path', '');
        if (str::endsWith($path, '/') === false) {
            $path .= '/';
        }

        $area = $this->areas->get($area);

        $finder = $this->findFiles($area->get('basepath'), $path);

        $media = $this->mediaRepository->findAll();

        $parent = $path !== '/' ? Path::canonicalize($path . '/..') : '';

        return $this->renderTemplate('finder/finder.html.twig', [
            'path' => $path,
            'name' => $area->get('name'),
            'area' => $area->get('key'),
            'finder' => $finder,
            'parent' => $parent,
            'media' => $media,
            'allfiles' => $area->get('show_all') ? $this->buildIndex($area->get('basepath')) : false,
        ]);
    }

    private function findFiles(string $base, string $path): Finder
    {
        $fullpath = Path::canonicalize($base . '/' . $path);

        $finder = new Finder();
        $finder->in($fullpath)->depth('== 0')->sortByName();

        return $finder;
    }

    private function buildIndex(string $base)
    {
        $fullpath = Path::canonicalize($base);

        $finder = new Finder();
        $finder->in($fullpath)->depth('< 5')->sortByName()->files();

        $index = [];

        foreach ($finder as $file) {
            $contents = current(explode("\n", $file->getContents()));
            $index[] = [
                'filename' => $file->getRelativePathname(),
                'description' => $contents,
            ];
        }

        return $index;
    }
}
