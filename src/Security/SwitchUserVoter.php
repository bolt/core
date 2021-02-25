<?php

declare(strict_types=1);

namespace Bolt\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SwitchUserVoter votes on the 'switch user' permission - the ability to impersonate another user. This is
 * extremely powerful and can be abused. Therefore it is not configurable in bolt's permissions.yaml.
 * This Voter is enabled by the following piece of symfony security configuration:
 *
 *     switch_user: { role: CAN_SWITCH_USER }
 *
 * in a firewall definition.
 * If it is configured like above only users with ROLE_DEVELOPER can impersonate.
 */
class SwitchUserVoter extends Voter
{
    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return $attribute === 'CAN_SWITCH_USER';
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access (or should we check $subject?)
        if (! $user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_DEVELOPER')) {
            return true;
        }

        return false;
    }
}
