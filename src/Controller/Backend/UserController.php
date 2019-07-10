<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController extends TwigAwareController implements BackendZone
{
    /** @var UserRepository */
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * @Route("/users", name="bolt_users")
     */
    public function users(): Response
    {
        $users = $this->users->findBy([], ['username' => 'ASC'], 1000);

        $twigVars = [
            'title' => 'Users & Permissions',
            'subtitle' => 'To edit users and their permissions',
            'users' => $users,
        ];

        return $this->renderTemplate('@bolt/users/listing.html.twig', $twigVars);
    }
}
