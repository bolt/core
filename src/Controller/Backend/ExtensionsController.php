<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Extension\BaseExtension;
use Bolt\Extension\ExtensionRegistry;
use ComposerPackages\Dependencies;
use ComposerPackages\Versions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('extensions')")
 */
class ExtensionsController extends AbstractController implements BackendZoneInterface
{
    /** @var ExtensionRegistry */
    private $extensionRegistry;

    /** @var Dependencies */
    private $dependenciesManager;

    public function __construct(ExtensionRegistry $extensionRegistry)
    {
        $this->extensionRegistry = $extensionRegistry;
        $this->dependenciesManager = new Dependencies();
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

    /**
     * @Route("/extensions/{name}", name="bolt_extensions_view", requirements={"name"=".+"})
     */
    public function viewExtension($name): Response
    {
        $name = str_replace('/', '\\', $name);

        /** @var BaseExtension $extension */
        $extension = $this->extensionRegistry->getExtension($name);
        $dependenciesNames = iterator_to_array($this->dependenciesManager->get($extension->getComposerPackage()->getName()));
        $dependencies = [];

        foreach ($dependenciesNames as $dependency) {
            $extDependency['name'] = $dependency;
            $extDependency['version'] = Versions::get($dependency);
            $dependencies[] = $extDependency;
        }

        $twigvars = [
            'extension' => $extension,
            'dependencies' => $dependencies,
        ];

        return $this->render('@bolt/pages/extension_details.html.twig', $twigvars);
    }
}
