<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Configuration\Config;
use Bolt\Controller\Frontend\FrontendZoneInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class MaintenanceModeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly Security $security
    ) {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the
        // controller is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof FrontendZoneInterface && $this->config->get('general/maintenance_mode', 'false') && ! $this->security->isGranted('maintenance-mode')) {
            throw new HttpException(503, 'Service Unavailable (Maintenance Mode)');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
