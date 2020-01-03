<?php


namespace Bolt\Log;


use Bolt\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class RequestProcessor
{
    /** @var RequestStack */
    protected $request;

    /** @var Security */
    private $security;

    public function __construct(RequestStack $request, Security $security)
    {
        $this->request = $request;
        $this->security = $security;
    }

    /**
     * @param array $record
     * @return array
     */
    public function processRecord(array $record): array
    {
        $req = $this->request->getCurrentRequest();

        /** @var User $user */
        $user = $this->security->getUser();

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7);

        $record['extra'] = [
            'client_ip'       => $req->getClientIp(),
            'client_port'     => $req->getPort(),
            'uri'          => $req->getUri(),
            'query_string'    => $req->getQueryString(),
            'method'          => $req->getMethod(),
            'request'         => $req->request->all()
            ];

        if ($user) {
            $record['user'] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ];
        }

        $record['location'] = [
            'file' => $trace[5]['file'],
            'line' => $trace[5]['line'],
            'class' => $trace[6]['class'],
            'type' => $trace[6]['type'],
            'function' => $trace[6]['function'],

        ];

        return $record;
    }
}