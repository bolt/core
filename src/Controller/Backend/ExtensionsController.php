<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Extension\ExtensionRegistry;
use Composer\Package\Package;
use Composer\Package\PackageInterface;
use ComposerPackages\Dependencies;
use ComposerPackages\Packages;
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

    /**
     * @var Dependencies
     */
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

        foreach ($extensions as $extension){
            $extension->dependencies = iterator_to_array($this->dependenciesManager->get("acmecorp/reference-extension"));
            $extension->dependencies = iterator_to_array(Dependencies::boltCommon());
        }

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
        $name = str_replace("/", "\\", $name);
        $extension = $this->extensionRegistry->getExtension($name);
        $extension->dependencies = iterator_to_array($this->dependenciesManager->get($extension->getComposerPackage()->getName()));

        $twigvars = [
            'extension' => $extension,
        ];

        return $this->render('@bolt/pages/extension_details.html.twig', $twigvars);
    }
}
