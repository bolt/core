<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Str;
use Bolt\Configuration\FileLocations;
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
     * @var FileLocations
     */
    private $fileLocations;

    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    public function __construct(FileLocations $fileLocations, MediaRepository $mediaRepository)
    {
        $this->fileLocations = $fileLocations;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @Route("/filemanager/{location}", name="bolt_filemanager", methods={"GET"})
     */
    public function filemanager(string $location, Request $request): Response
    {
        $path = $request->query->get('path', '');
        if (str::endsWith($path, '/') === false) {
            $path .= '/';
        }

        $location = $this->fileLocations->get($location);

        $finder = $this->findFiles($location->getBasepath(), $path);

        $media = $this->mediaRepository->findAll();

        $parent = $path !== '/' ? Path::canonicalize($path . '/..') : '';

        return $this->renderTemplate('finder/finder.html.twig', [
            'path' => $path,
            'name' => $location->getName(),
            'location' => $location->getKey(),
            'finder' => $finder,
            'parent' => $parent,
            'media' => $media,
            'allfiles' => $location->isShowAll() ? $this->buildIndex($location->getBasepath()) : false,
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
