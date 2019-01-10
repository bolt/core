<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Str;
use Bolt\Configuration\Areas;
use Bolt\Configuration\Config;
use Bolt\Controller\BaseController;
use Bolt\Repository\MediaRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Webmozart\PathUtil\Path;

/**
 * Class EditRecordController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class FilemanagerController extends BaseController
{
    /** @var Areas */
    private $areas;

    public function __construct(Config $config, CsrfTokenManagerInterface $csrfTokenManager, Areas $areas)
    {
        parent::__construct($config, $csrfTokenManager);

        $this->areas = $areas;
    }

    /**
     * @Route("/filemanager/{area}", name="bolt_filemanager", methods={"GET"})
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function filemanager(string $area, Request $request, MediaRepository $mediaRepository): Response
    {
        $path = $request->query->get('path');
        if (! str::endsWith($path, '/')) {
            $path .= '/';
        }

        $area = $this->areas->get($area);

        $finder = $this->findFiles($area->get('basepath'), $path);

        $media = $mediaRepository->findAll();

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
