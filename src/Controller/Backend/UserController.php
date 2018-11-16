<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\BaseController;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController extends BaseController
{
    /**
     * @Route("/users", name="bolt_users")
     */
    public function users()
    {
        return $this->renderTemplate('pages/about.twig');
    }

    /**
     * @Route("/profile-edit", methods={"GET"}, name="bolt_profile_edit")
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();

        return $this->renderTemplate('users/edit.twig', [
            'usertitle' => $user->getFullName(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile-edit", methods={"POST"}, name="bolt_profile_edit_post")
     */
    public function edit_post(Request $request, UrlGeneratorInterface $urlGenerator,
                              ObjectManager $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
        $userTitle = $user->getFullName();
        $url = $urlGenerator->generate('bolt_profile_edit');
        $locale = $request->get('user')['locale'];
        $newPassword = $request->get('password');

        $user->setFullName($request->get('fullName'));
        $user->setEmail($request->get('email'));
        $user->setLocale($locale);
        $user->setbackendTheme($request->get('user')['backendTheme']);

        $hasError = false;

        $usernameValidateOptions = [
            'options' => [
                'min_range' => 1,
            ],
        ];

        // Validate username
        if (!filter_var(strlen($user->getFullName()), FILTER_VALIDATE_INT, $usernameValidateOptions)) {
            $this->addFlash('danger', 'user.not_valid_username');
            $hasError = true;
        }

        // Validate password
        if (!empty($newPassword) && strlen($newPassword) < 6) {
            $this->addFlash('danger', 'user.not_valid_password');
            $hasError = true;
        } elseif(!empty($newPassword) && strlen($newPassword) > 6) {
            $user->setPassword($encoder->encodePassword($user, $newPassword));
        }

        // Validate email
        if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'user.not_valid_email');
            $hasError = true;
        };

        if($hasError){
            return $this->renderTemplate('users/edit.twig', [
                'usertitle' => $userTitle,
                'user'     => $user,
            ]);
        }

        $manager->flush();

        $request->getSession()->set('_locale', $locale);

        return new RedirectResponse($url);

    }
}
