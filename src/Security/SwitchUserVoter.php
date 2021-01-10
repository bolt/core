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
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return $attribute === 'CAN_SWITCH_USER' && $subject === null;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous or if the subject is set, do not grant access
        if (! $user instanceof UserInterface || $subject !== null) {
            return false;
        }

        if ($this->security->isGranted('ROLE_DEVELOPER')) {
            return true;
        }

        /*
         * or use some custom data from your User object
        if ($user->isAllowedToSwitch()) {
            return true;
        }
        */

        return false;
    }
}
