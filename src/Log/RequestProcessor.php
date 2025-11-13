<?php

declare(strict_types=1);

namespace Bolt\Log;

use Bolt\Entity\User;
use Monolog\LogRecord;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class RequestProcessor
{
    private readonly string $projectDir;

    public function __construct(
        protected RequestStack $request,
        private readonly Security $security,
        KernelInterface $kernel
    ) {
        $this->projectDir = $kernel->getProjectDir();
    }

    public function processRecord(LogRecord $record): LogRecord
    {
        $request = $this->request->getCurrentRequest();

        /** @var User $user */
        $user = $this->security->getUser();

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7);

        if ($request instanceof Request) {
            $record->extra['client_ip'] = $request->getClientIp();
            $record->extra['client_port'] = $request->getPort();
            $record->extra['uri'] = $request->getUri();
            $record->extra['query_string'] = $request->getQueryString();
            $record->extra['method'] = $request->getMethod();
        }

        if ($user instanceof User) {
            $record->extra['user'] = [
                'id' => $user->getId(),
                'username' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ];
        }

        $record->extra['location'] = [
            'file' => '…/' . Path::makeRelative($trace[5]['file'], $this->projectDir),
            'line' => $trace[5]['line'],
            'class' => $trace[6]['class'],
            'type' => $trace[6]['type'],
            'function' => $trace[6]['function'],
        ];

        return $record;
    }
}
