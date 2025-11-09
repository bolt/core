<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Configuration\Config;
use Twig\Environment;
use Twig\Markup;

class Notifications
{
    public function __construct(
        private readonly Environment $environment,
        private readonly Config $config
    ) {
    }

    public function success(string $subject, string $body): null
    {
        $this->render('Success: ' . $subject, $body, 'success');

        return null;
    }

    public function danger(string $subject, string $body): null
    {
        $this->render('Danger: ' . $subject, $body, 'danger');

        return null;
    }

    public function info(string $subject, string $body): null
    {
        $this->render('Info: ' . $subject, $body, 'info');

        return null;
    }

    public function warning(string $subject, string $body): null
    {
        $this->render('Warning: ' . $subject, $body, 'warning');

        return null;
    }

    private function render(string $subject, string $body, string $type): void
    {
        $twigVars = [
            'subject' => $subject,
            'body' => $body,
            'type' => $type,
            'basePath' => $this->config->getPath('site'),
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7),
        ];

        $output = $this->environment->render('@bolt/_partials/notification.html.twig', $twigVars);

        echo new Markup($output, 'utf-8');
    }
}
