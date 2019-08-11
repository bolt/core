<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Extension\ExtensionRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ExtensionsController extends AbstractController implements BackendZone
{
    /** @var ExtensionRegistry */
    private $extensionRegistry;

    public function __construct(ExtensionRegistry $extensionRegistry)
    {
        $this->extensionRegistry = $extensionRegistry;
    }

    /**
     * @Route("/extensions", name="bolt_extensions")
     */
    public function index(): Response
    {
        $extensions = $this->extensionRegistry->getExtensions();

        $twigvars = [
            'extensions' => $extensions,
        ];

        return $this->render('@bolt/pages/extensions.html.twig', $twigvars);
    }
}
