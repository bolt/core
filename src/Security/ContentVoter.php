<?php


namespace Bolt\Security;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\User;

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
# - view: allows viewing records in the backend
     */
    public const CONTENT_EDIT = 'content_edit';
    public const CONTENT_CREATE = 'content_create';
    public const CONTENT_PUBLISH = 'content_publish';
    public const CONTENT_DEPUBLISH = 'content_depublish';
    public const CONTENT_DELETE = 'content_delete';
    public const CONTENT_CHANGE_OWNERSHIP = 'content_change_ownership';
    public const CONTENT_VIEW = 'content_view';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::CONTENT_EDIT, self::CONTENT_CREATE, self::CONTENT_PUBLISH,
            self::CONTENT_DEPUBLISH, self::CONTENT_DELETE, self::CONTENT_CHANGE_OWNERSHIP,
            self::CONTENT_VIEW])) {
            return false;
        }

        // only vote on `Post` objects
//        if (!$subject instanceof Post) {
//            return false;
//        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Post $post */
        $post = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($post, $user);
            case self::EDIT:
                return $this->canEdit($post, $user);
        }

        throw new \LogicException('This code should not be reached!');

    }

    private function canView(Post $post, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($post, $user)) {
            return true;
        }

        // the Post object could have, for example, a method `isPrivate()`
        return !$post->isPrivate();
    }

    private function canEdit(Post $post, User $user)
    {
        // this assumes that the Post object has a `getOwner()` method
        return $user === $post->getOwner();
    }
}