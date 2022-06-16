<?php

declare(strict_types=1);

namespace Bolt\Log;

use Bolt\Entity\User;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;

class RequestProcessor
{
    /** @var RequestStack */
    protected $request;

    /** @var Security */
    private $security;

    /** @var string */
    private $projectDir;

    public function __construct(RequestStack $request, Security $security, KernelInterface $kernel)
    {
        $this->request = $request;
        $this->security = $security;
        $this->projectDir = $kernel->getProjectDir();
    }

    public function processRecord(array $record): array
    {
        $request = $this->request->getCurrentRequest();

        /** @var User $user */
        $user = $this->security->getUser();

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7);

        if (! empty($request)) {
            $record['extra'] = [
                'client_ip' => $request->getClientIp(),
                'client_port' => $request->getPort(),
                'uri' => $request->getUri(),
                'query_string' => $request->getQueryString(),
                'method' => $request->getMethod(),
            ];
        }

        if ($user instanceof User) {
            $record['user'] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ];
        }

        $record['location'] = [
            'file' => '…/' . Path::makeRelative($trace[5]['file'], $this->projectDir),
            'line' => $trace[5]['line'],
            'class' => $trace[6]['class'],
            'type' => $trace[6]['type'],
            'function' => $trace[6]['function'],
        ];

        return $record;
    }
}
