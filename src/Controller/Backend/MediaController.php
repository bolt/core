<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Areas;
use Bolt\Configuration\Config;
use Bolt\Content\MediaFactory;
use Bolt\Controller\BaseController;
use Bolt\Entity\Media;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Webmozart\PathUtil\Path;

/**
 * Class MediaController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class MediaController extends BaseController
{
    /** @var ObjectManager */
    private $manager;

    /** @var Areas */
    private $areas;

    /** @var MediaFactory */
    private $mediaFactory;

    /**
     * MediaController constructor.
     */
    public function __construct(Config $config, CsrfTokenManagerInterface $csrfTokenManager, ObjectManager $manager, Areas $areas, MediaFactory $mediaFactory)
    {
        parent::__construct($config, $csrfTokenManager);

        $this->manager = $manager;
        $this->areas = $areas;
        $this->mediaFactory = $mediaFactory;
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

            $this->manager->persist($media);
            $this->manager->flush();
        }

        return $this->renderTemplate('finder/finder.twig', [
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
