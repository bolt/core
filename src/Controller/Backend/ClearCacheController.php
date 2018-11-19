<?php

namespace Bolt\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Bolt\Controller\BaseController;

class ClearCacheController extends BaseController
{
    /**
     * @Route("/clearcache", name="bolt_clear_cache")
     */
    public function index( KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'cache:clear'
        ));

        $application->run($input);
        $this->addFlash('success', '');

        return $this->render('clearcache/clearcache.html.twig');
    }
}
