<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Areas;
use Bolt\Configuration\Config;
use Bolt\Content\MediaFactory;
use Bolt\Controller\TwigAwareController;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Webmozart\PathUtil\Path;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class MediaController extends TwigAwareController
{
    /** @var ObjectManager */
    private $em;

    /** @var Areas */
    private $areas;

    /** @var MediaFactory */
    private $mediaFactory;

    public function __construct(Config $config, Environment $twig, ObjectManager $em, Areas $areas, MediaFactory $mediaFactory)
    {
        $this->em = $em;
        $this->areas = $areas;
        $this->mediaFactory = $mediaFactory;
        parent::__construct($config, $twig);
    }

    /**
     * @Route("/media/crawl/{area}", name="bolt_media_crawler", methods={"GET"})
     */
    public function finder(string $area): Response
    {
        $basepath = $this->areas->get($area, 'basepath');

        $finder = $this->findFiles($basepath);

        foreach ($finder as $file) {
            $media = $this->mediaFactory->createOrUpdateMedia($file, $area);

            $this->em->persist($media);
            $this->em->flush();
        }

        return $this->renderTemplate('@bolt/finder/finder.twig', [
            'path' => 'path',
            'name' => $this->areas->get($area, 'name'),
            'area' => $area,
            'finder' => $finder,
            'parent' => 'parent',
        ]);
    }

    private function findFiles(string $base): Finder
    {
        $fullpath = Path::canonicalize($base);

        $glob = sprintf('*.{%s}', $this->config->getMediaTypes()->implode(','));

        $finder = new Finder();
        $finder->in($fullpath)->depth('< 2')->sortByName()->name($glob)->files();

        return $finder;
    }
}
