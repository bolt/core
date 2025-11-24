<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Repository\UserRepository;
use Bolt\Storage\Query;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Display the list of users, along with buttons to change them.
 */
#[IsGranted(attribute: 'user:list')]
class UserController extends TwigAwareController implements BackendZoneInterface
{
    private const PAGESIZE = 20;

    public function __construct(
        private readonly UserRepository $users
    ) {
    }

    #[Route(path: '/users', name: 'bolt_users')]
    public function users(Request $request, Query $query): Response
    {
        $order = 'username';
        if ($request->get('sortBy')) {
            $order = $this->getFromRequest($request, 'sortBy');
        }

        $like = '';
        if ($request->get('filter')) {
            $like = '%' . $this->getFromRequest($request, 'filter') . '%';
        }

        $users = new ArrayAdapter($this->users->findUsers($like, $order));
        $currentPage = (int) $this->getFromRequest($request, 'page', '1');
        $users = new Pagerfanta($users);
        $users->setMaxPerPage(self::PAGESIZE)
            ->setCurrentPage($currentPage);

        $twigVars = [
            'title' => 'controller.user.title',
            'subtitle' => 'controller.user.subtitle',
            'users' => $users,
            'sortBy' => $this->getFromRequest($request, 'sortBy'),
            'filterValue' => $this->getFromRequest($request, 'filter'),
        ];

        return $this->render('@bolt/users/listing.html.twig', $twigVars);
    }
}
