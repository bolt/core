<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\FileLocations;
use Bolt\Controller\TwigAwareController;
use Bolt\Factory\MediaFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\PathUtil\Path;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class MediaController extends TwigAwareController implements BackendZoneInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var FileLocations */
    private $fileLocations;

    /** @var MediaFactory */
    private $mediaFactory;

    public function __construct(EntityManagerInterface $em, FileLocations $fileLocations, MediaFactory $mediaFactory)
    {
        $this->em = $em;
        $this->fileLocations = $fileLocations;
        $this->mediaFactory = $mediaFactory;
    }

    /**
     * @Route("/media/crawl/{location}", name="bolt_media_crawler", methods={"GET"})
     */
    public function finder(string $locationName): Response
    {
        $basepath = $this->fileLocations->get($locationName)->getBasepath();

        $finder = $this->findFiles($basepath);

        foreach ($finder as $file) {
            $media = $this->mediaFactory->createOrUpdateMedia($file, $locationName);

            $this->em->persist($media);
        }

        $this->em->flush();

        return $this->render('@bolt/finder/finder.twig', [
            'path' => 'path',
            'name' => $this->fileLocations->get($locationName)->getName(),
            'location' => $locationName,
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
