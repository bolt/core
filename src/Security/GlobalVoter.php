<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Configuration\Config;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\User;

class GlobalVoter extends Voter
{
    public const VIEW_SETTINGS = 'view_settings';
    public const EDIT_SETTINGS = 'edit_settings';

    /*
# The first set of permissions are the 'global' permissions; these are not tied
# to any content types, but rather apply to global, non-content activity in
# Bolt's backend. Most of these permissions map directly to backend routes;
# keep in mind, however, that routes do not always correspond to URL paths 1:1.
# The default set defined here is appropriate for most sites, so most likely,
# you will not have to change it.
# Also note that the 'editcontent' and 'overview' routes are special-cased
# inside the code, so they don't appear here.
global:
    about: [ everyone ] # view the 'About Bolt' page
    checks: [ admin, developer ]
    clearcache: [ admin, developer ]
    contentaction: [ editor, admin, developer ]
    dashboard: [ everyone ]
    dbcheck: [ admin, developer ]
    dbupdate: [ admin, developer ]
    dbupdate_result: [ admin, developer ]
    extensions: [ developer ]
    extensions:config: [ developer ]
    fileedit: [ admin, developer ]
    files:config: [ developer ]
    files:hidden: [ developer ]
    files:theme: [ developer ]
    files:uploads: [ admin, developer, chief-editor, editor ]
    files: [ admin, developer, chief-editor, editor ]
    prefill: [ developer ]
    profile: [ everyone ] # edit own profile
    settings: [ admin, developer, everyone ]
    translation: [ developer ]
    useraction: [ admin, developer ] # enable/disable/delete
    useredit: [ admin, developer ] # user settings
    users: [ admin, developer ] # view user overview
    roles: [ admin, developer ] # view the roles overview
    maintenance-mode: [ everyone ] # view the frontend when in maintenance mode
    omnisearch: [ everyone ]
    # Access to the various logs
    changelog: [ admin, developer, chief-editor ]
    systemlog: [ admin, developer ]
    # The following permissions are particularly important: login and postLogin
    # determine who may see and use the login form. If you set them to anything
    # but 'anonymous', only users will be able to log in that are logged in
    # already, which is probably never what you want.
    login: [ anonymous ]
    postLogin: [ anonymous ]
    # Likewise, 'logout' needs to be granted to 'everyone', otherwise people
    # cannot log out anymore.
    logout: [ everyone ]
     */

    private $security;
    private $config;

    public function __construct(Security $security, Config $config)
    {
        $this->security = $security;
        $this->config = $config->get('permissions');
    }

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (! in_array($attribute, [self::VIEW_SETTINGS, self::EDIT_SETTINGS], true)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        if (! $user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case self::VIEW_SETTINGS:
                return true;
            case self::EDIT_SETTINGS:
                return true;
        }

        throw new \LogicException('This code should not be reached!');
    }
}
