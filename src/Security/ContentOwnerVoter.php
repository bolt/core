<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Entity\Content;
use Bolt\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ContentOwnerVoter extends Voter
{
    public const OWNER = 'CONTENT_OWNER';

    protected function supports(string $attribute, $subject): bool
    {
        // only vote on `Content`
        return $attribute === self::OWNER && $subject instanceof Content;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Notice the check on a _Bolt_ user entity, other user classes that might implement UserInterface
        // cannot 'own' bolt content
        if (! $user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var User $boltUser */
        $boltUser = $user;

        /** @var Content $content */
        $content = $subject;

        $author = $content->getAuthor();

        return $author && ($author->getId() === $boltUser->getId());
    }
}
