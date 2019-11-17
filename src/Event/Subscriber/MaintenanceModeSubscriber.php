<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Configuration\Config;
use Bolt\Controller\Frontend\FrontendZone;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class MaintenanceModeSubscriber implements EventSubscriberInterface
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }
    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the
        // controller is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof FrontendZone && $this->config->get('general/maintenance_mode', 'false')) {
            throw new HttpException(503, 'Service Unavailable (Maintenance Mode)');
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
