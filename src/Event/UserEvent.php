<?php

declare(strict_types=1);

namespace Bolt\Event;

use Bolt\Entity\User;
use Illuminate\Support\Collection;

class UserEvent
{
    public const ON_ADD = 'bolt.users_pre_add';
    public const ON_EDIT = 'bolt.users_pre_edit';
    public const ON_PRE_SAVE = 'bolt.users_post_save';
    public const ON_POST_SAVE = 'bolt.users_post_save';

    /** @var Collection */
    private $rolesOptions;

    public function __construct(
        private readonly User $user,
        ?Collection $roleOptions = null
    ) {
        if (! $roleOptions) {
            $this->rolesOptions = collect([]);
        } else {
            $this->rolesOptions = $roleOptions;
        }
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRoleOptions(): Collection
    {
        return $this->rolesOptions;
    }

    public function setRoleOptions(Collection $roleOptions): void
    {
        $this->rolesOptions = $roleOptions;
    }
}
