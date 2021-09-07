<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Config;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Version;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class GeneralController extends TwigAwareController implements BackendZoneInterface
{
    /** @var \Bolt\Doctrine\Version */
    private $doctrineVersion;

    public function __construct(\Bolt\Doctrine\Version $doctrineVersion)
    {
        $this->doctrineVersion = $doctrineVersion;
    }

    /**
     * @Route("/about", name="bolt_about")
     */
    public function about(): Response
    {
        $twigVars = [
            'installType' => Version::installType(),
            'platform' => $this->doctrineVersion->getPlatform(),
            'php' => PHP_VERSION,
            'symfony' => Version::getSymfonyVersion(),
            'os_name' => php_uname('s'),
            'os_version' => php_uname('r'),
            'memory_limit' => ini_get('memory_limit'),
        ];

        return $this->render('@bolt/pages/about.html.twig', $twigVars);
    }

    /**
     * @Route("/kitchensink", name="bolt_kitchensink")
     */
    public function kitchensink(ContentRepository $content, Config $config): Response
    {
        $contentTypes = $config->get('contenttypes');

        /** @var Content $records */
        $records = $content->findLatest($contentTypes, 1, 4);

        $this->addFlash('success', '<strong>Well done!</strong> You successfully read this important alert message.');
        $this->addFlash('info', '<strong>Heads up!</strong> This alert needs your attention, but it\'s not super important.');
        $this->addFlash('warning', '<strong>Warning!</strong> Better check yourself, you\'re not looking too good.');
        $this->addFlash('danger', '<strong>Oh snap!</strong> Change a few things up and try submitting again.');

        $twigVars = [
            'title' => 'Kitchensink',
            'subtitle' => 'To show a number of different things, on one page',
            'records' => $records,
        ];

        return $this->render('@bolt/pages/kitchensink.html.twig', $twigVars);
    }
}
