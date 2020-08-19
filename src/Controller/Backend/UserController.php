<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Repository\UserRepository;
use Bolt\Storage\Query;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserController extends TwigAwareController implements BackendZoneInterface
{
    /** @var UserRepository */
    private $users;

    private const PAGESIZE = 20;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * @Route("/users", name="bolt_users")
     */
    public function users(Query $query): Response
    {
        $users = new ArrayAdapter($this->users->findBy([], ['username' => 'ASC'], 1000));
        $currentPage = (int) $this->getFromRequest('page', '1');
        $users = new Pagerfanta($users);
        $users->setMaxPerPage(self::PAGESIZE)
            ->setCurrentPage($currentPage);

        $twigVars = [
            'title' => 'controller.user.title',
            'subtitle' => 'controller.user.subtitle',
            'users' => $users,
        ];

        return $this->render('@bolt/users/listing.html.twig', $twigVars);
    }
}
