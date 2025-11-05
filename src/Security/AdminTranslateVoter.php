<?php

declare(strict_types=1);

namespace Bolt\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminTranslateVoter extends Voter
{
    public const ADMIN_TRANSLATE_ACCESS = 'ADMIN_TRANSLATE_ACCESS';

    public function __construct(
        private readonly Security $security
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::ADMIN_TRANSLATE_ACCESS;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return $this->security->isGranted('translation');
    }
}
