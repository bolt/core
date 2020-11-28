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

    private $security;
    private $supportedAttributes;

    /** @var Collection|null */
    private $contenttypePermissionsAll;
    /** @var Collection|null */
    private $contenttypePermissionsDefault;
    /** @var Collection|null */
    private $contenttypePermissions;

    public function __construct(Security $security, Config $config)
    {
        $this->security = $security;

        $this->contenttypePermissionsAll = $config->get('permissions/contenttype-all', collect([]));
        $this->contenttypePermissionsDefault = $config->get('permissions/contenttype-default', collect([]));
        $this->contenttypePermissions = $config->get('permissions/contenttypes', null);

        if (! ($this->contenttypePermissionsAll instanceof Collection)) {
            throw new \DomainException('No permissions config found');
        }
        if (! ($this->contenttypePermissionsDefault instanceof Collection)) {
            throw new \DomainException('No permissions config found');
        }
        if (! ($this->contenttypePermissions == null || $this->contenttypePermissions instanceof Collection)) {
            throw new \DomainException('No permissions config found');
        }
    }

    protected function supports(string $attribute, $subject)
    {
        // only vote on `Content` and `ContentType` objects
        if (! ($subject instanceof Content || $subject instanceof ContentType)) {
            return false;
        }

        // if the attribute isn't one we support, return false
        return in_array($attribute, [self::CONTENT_EDIT, self::CONTENT_CREATE, self::CONTENT_CHANGE_STATUS,
            self::CONTENT_DELETE, self::CONTENT_CHANGE_OWNERSHIP, self::CONTENT_VIEW], true);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (! $user instanceof UserInterface) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // first check if the users has an 'all' permission set for this content(type)
        $allRoles = $this->contenttypePermissionsAll->get($attribute);
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
        } else if ($subject instanceof ContentType) {
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
        $contentTypeDefaultPermissions = $this->contenttypePermissionsDefault;
        if ($contentTypeDefaultPermissions) {
            $roles = $contentTypeDefaultPermissions->get($attribute);

            if ($roles) {
                // check if user is granted any of the specified attributes/roles
                return $this->isGrantedAny($roles, $subject);
            }
        }

        // apparently there was no match -> deny!
        return false;

//        // Or do we always want to have a 'complete' setup, and throw an error here?
//        throw new \LogicException('This code should not be reached!');
    }

    private function isGrantedAny($attributes, $subject = null)
    {
        foreach ($attributes as $attribute) {
            if ($this->security->isGranted($attribute, $subject)) {
                return true;
            }
        }
        return false;
    }
}
