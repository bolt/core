<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Tightenco\Collect\Support\Collection;

class ContentVoter extends Voter
{
    /*
# The following permissions are available on a per-contenttype basis:
#
# - edit: allows updating existing records
# - create: allows creating new records
# - publish: allows changing the status of a record to "published", as well as
#            scheduling a record for future publishing
# - depublish: allows changing the status of a record from "published"
# - delete: allows (hard) deletion of records
# - change-ownership: allows changing a record's owner. Note that ownership may
#                     grant additional permissions on a record, so this
#                     permission can indirectly enable users more permissions
#                     in ways that may not be immediately obvious.
     */
    public const CONTENT_EDIT = 'edit';
    public const CONTENT_CREATE = 'create';
    public const CONTENT_CHANGE_STATUS = 'change-status';
    public const CONTENT_DELETE = 'delete';
    public const CONTENT_CHANGE_OWNERSHIP = 'change-ownership';
    public const CONTENT_VIEW = 'view';
    // used to determine of user can view an entry or see the listing/menu for it
    // this permission is not to be specified in the config, it is only used internally
    public const CONTENT_MENU_LISTING = 'menu_listing';

    /** @var Security */
    private $security;

    /** @var Collection|null */
    private $contenttypeBasePermissions;

    /** @var Collection|null */
    private $contenttypeDefaultPermissions;

    /** @var Collection|null */
    private $contenttypePermissions;

    public function __construct(Security $security, Config $config)
    {
        $this->security = $security;

        $this->contenttypeBasePermissions = $config->get('permissions/contenttype-base', collect([]));
        $this->contenttypeDefaultPermissions = $config->get('permissions/contenttype-default', collect([]));
        $this->contenttypePermissions = $config->get('permissions/contenttypes', null);

        if (! ($this->contenttypeBasePermissions instanceof Collection)) {
            throw new \DomainException('No suitable contenttype-base permissions config found');
        }
        if (! ($this->contenttypeDefaultPermissions instanceof Collection)) {
            throw new \DomainException('No suitable contenttype-default permissions config found');
        }
        if (! ($this->contenttypePermissions === null || $this->contenttypePermissions instanceof Collection)) {
            throw new \DomainException('No suitable contenttypes permissions config found');
        }
    }

    protected function supports(string $attribute, $subject): bool
    {
        // only vote on `Content` and `ContentType` objects
        if (! ($subject instanceof Content || $subject instanceof ContentType)) {
            return false;
        }

        // if the attribute isn't one we support, return false
        return in_array($attribute, [self::CONTENT_EDIT, self::CONTENT_CREATE, self::CONTENT_CHANGE_STATUS,
            self::CONTENT_DELETE, self::CONTENT_CHANGE_OWNERSHIP, self::CONTENT_VIEW, self::CONTENT_MENU_LISTING, ], true);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (! $user instanceof UserInterface) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // special case for CONTENT_MENU_LISTING
        if ($attribute === self::CONTENT_MENU_LISTING) {
            return $this->voteOnAttribute(self::CONTENT_CREATE, $subject, $token)
                || $this->voteOnAttribute(self::CONTENT_DELETE, $subject, $token)
                || $this->voteOnAttribute(self::CONTENT_EDIT, $subject, $token)
                || $this->voteOnAttribute(self::CONTENT_CHANGE_STATUS, $subject, $token)
                || $this->voteOnAttribute(self::CONTENT_CHANGE_OWNERSHIP, $subject, $token)
                || $this->voteOnAttribute(self::CONTENT_VIEW, $subject, $token)
                ;
        }

        // special case for CONTENT_VIEW -> we'll also grant this to users that have any of these edit/delete permissions
        // if the user has none of these, continue the function below to check for the 'real' CONTENT_VIEW permission
        if ($attribute === self::CONTENT_VIEW && $this->isGrantedAny([self::CONTENT_EDIT, self::CONTENT_CHANGE_STATUS,
            self::CONTENT_DELETE, self::CONTENT_CHANGE_OWNERSHIP, ], $subject)) {
            return true;
        }

        // first check if the user has a 'base' permission set for this content(type)
        $allRoles = $this->contenttypeBasePermissions->get($attribute);
        if ($allRoles && $allRoles instanceof Collection) {
            // check if user is granted any of the specified attributes/roles
            foreach ($allRoles as $role) {
                if ($this->security->isGranted($role, $subject)) {
                    return true;
                }
            }
        }

        $contentTypeName = null;
        if ($subject instanceof Content) {
            /** @var Content $content */
            $content = $subject;
            $contentTypeName = $content->getContentType();
        } elseif ($subject instanceof ContentType) {
            /** @var ContentType $contentType */
            $contentType = $subject;
            $contentTypeName = $contentType->getSlug();
        } else {
            return false;
        }

        // try to find a contenttype specific setting first
        if ($this->contenttypePermissions) {
            $contenTypePermissions = $this->contenttypePermissions->get($contentTypeName);
            if ($contenTypePermissions) {
                $roles = $contenTypePermissions->get($attribute);
                if ($roles) {
                    // check if user is granted any of the specified attributes/roles
                    return $this->isGrantedAny($roles, $subject);
                }
            }
        }

        // if there was no specific rule for this contenttype + attribute, fall back to the default
        $contentTypeDefaultPermissions = $this->contenttypeDefaultPermissions;
        if ($contentTypeDefaultPermissions) {
            $roles = $contentTypeDefaultPermissions->get($attribute);

            if ($roles) {
                // check if user is granted any of the specified attributes/roles
                return $this->isGrantedAny($roles, $subject);
            }
        }

        // apparently there was no match -> deny!
        return false;
    }

    private function isGrantedAny($attributes, $subject = null): bool
    {
        foreach ($attributes as $attribute) {
            if ($this->security->isGranted($attribute, $subject)) {
                return true;
            }
        }

        return false;
    }
}
