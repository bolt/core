<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Areas;
use Bolt\Configuration\Config;
use Bolt\Content\MediaFactory;
use Bolt\Controller\BaseController;
use Bolt\Entity\Media;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tightenco\Collect\Support\Collection;
use Webmozart\PathUtil\Path;

/**
 * Class MediaController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class MediaController extends BaseController
{
    /** @var ObjectManager */
    private $manager;

    /** @var Collection */
    private $areas;

    /** @var MediaFactory */
    private $mediaFactory;

    /**
     * MediaController constructor.
     *
     * @param Config        $config
     * @param ObjectManager $manager
     * @param Areas         $areas
     * @param MediaFactory  $mediaFactory
     */
    public function __construct(Config $config, ObjectManager $manager, Areas $areas, MediaFactory $mediaFactory)
    {
        $this->config = $config;
        $this->manager = $manager;
        $this->areas = $areas;

        $this->mediaFactory = $mediaFactory;
    }

    /**
     * @Route("/media/crawl/{area}", name="bolt_media_crawler", methods={"GET"})
     */
    public function finder($area, Request $request)
    {
        $basepath = $this->areas->get($area, 'basepath');

        $finder = $this->findFiles($basepath);

        foreach ($finder as $file) {
            $media = $this->mediaFactory->createOrUpdateMedia($file, $area);

            $this->manager->persist($media);
            $this->manager->flush();
        }

        return $this->renderTemplate('finder/finder.twig', [
            'path' => $path,
            'name' => $areas[$area]['name'],
            'area' => $area,
            'finder' => $finder,
            'parent' => $parent,
            'allfiles' => $areas[$area]['show_all'] ? $this->buildIndex($basepath) : false,
        ]);
    }

    private function findFiles($base)
    {
        $fullpath = Path::canonicalize($base);

        $glob = sprintf('*.{%s}', $this->config->getMediaTypes()->implode(','));

        $finder = new Finder();
        $finder->in($fullpath)->depth('< 2')->sortByName(true)->name($glob)->files();

        return $finder;
    }
}
