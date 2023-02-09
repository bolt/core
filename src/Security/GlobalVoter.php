<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Configuration\Config;
use Bolt\Entity\User;
use Bolt\Enum\UserStatus;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Tightenco\Collect\Support\Collection;

class GlobalVoter extends Voter
{
    /** @var Security */
    private $security;

    /** @var Collection */
    private $globalPermissions;

    /** @var array */
    private $supportedAttributes;

    public function __construct(Security $security, Config $config)
    {
        $this->security = $security;
        $this->globalPermissions = $config->get('permissions/global');

        if ($this->globalPermissions instanceof Collection) {
            $this->supportedAttributes = $this->globalPermissions->keys()->toArray();
        } else {
            throw new \DomainException('No global permissions config found');
        }
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, $this->supportedAttributes, true);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Deny if the user is a Bolt\Entity\User and not enabled
        if ($user instanceof User && $user->getStatus() !== UserStatus::ENABLED) {
            return false;
        }

        if ($attribute === 'user:status') {
            // users with 'user:edit' also have 'user:status' permission
            if ($this->voteOnAttribute('user:edit', $subject, $token)) {
                return true;
            }
        }

        if (! isset($this->globalPermissions[$attribute])) {
            throw new \DomainException("Global permission '{$attribute}' not defined, check your security and permissions configuration.");
        }

        $rolesWithPermission = $this->globalPermissions[$attribute];

        foreach ($rolesWithPermission as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return false;
    }
}
