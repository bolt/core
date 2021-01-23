<?php

declare(strict_types=1);

namespace Bolt\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class AdminApiVoter extends Voter
{
    public const ADMIN_API_ACCESS = 'ADMIN_API_ACCESS';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        return $attribute === self::ADMIN_API_ACCESS;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        return $this->security->isGranted('api_admin');
    }
}
