<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ContentOwnerVoter extends Voter
{
    public const OWNER = 'OWNER';

    private $security;

    public function __construct(Security $security, Config $config)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        // only vote on `Content`
        if (! ($subject instanceof Content)) {
            return false;
        }

        return $attribute === self::OWNER;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // Notice the check on a _Bolt_ user, other user classes that might implement UserInterface
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
