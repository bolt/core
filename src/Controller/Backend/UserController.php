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
 * Display the list of users, along with buttons to change them.
 *
 * @Security("is_granted('user:list')")
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
        $order = 'username';
        if ($this->request->get('sortBy')) {
            $order = $this->getFromRequest('sortBy');
        }

        $like = '';
        if ($this->request->get('filter')) {
            $like = '%' . $this->getFromRequest('filter') . '%';
        }

        $users = new ArrayAdapter($this->users->findUsers($like, $order));
        $currentPage = (int) $this->getFromRequest('page', '1');
        $users = new Pagerfanta($users);
        $users->setMaxPerPage(self::PAGESIZE)
            ->setCurrentPage($currentPage);

        $twigVars = [
            'title' => 'controller.user.title',
            'subtitle' => 'controller.user.subtitle',
            'users' => $users,
            'sortBy' => $this->getFromRequest('sortBy'),
            'filterValue' => $this->getFromRequest('filter'),
        ];

        return $this->render('@bolt/users/listing.html.twig', $twigVars);
    }
}
