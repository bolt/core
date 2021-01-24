<?php

declare(strict_types=1);

namespace Bolt\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class AdminTranslateVoter extends Voter
{
    public const ADMIN_TRANSLATE_ACCESS = 'ADMIN_TRANSLATE_ACCESS';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        return $attribute === self::ADMIN_TRANSLATE_ACCESS;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        return $this->security->isGranted('translation');
    }
}
