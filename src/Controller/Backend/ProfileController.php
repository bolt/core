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
class ProfileController extends BaseController
{
    /**
     * @Route("/profile-edit", methods={"GET"}, name="bolt_profile_edit")
     *
     * @param Request $request
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function profileEdit(Request $request): Response
    {
        $user = $this->getUser();

        return $this->renderTemplate('users/edit.html.twig', [
            'usertitle' => $user->getFullName(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile-edit", methods={"POST"}, name="bolt_profile_edit_post")
     *
     * @param Request                      $request
     * @param UrlGeneratorInterface        $urlGenerator
     * @param ObjectManager                $manager
     * @param UserPasswordEncoderInterface $encoder
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function profileEditPost(Request $request, UrlGeneratorInterface $urlGenerator, ObjectManager $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
        $userTitle = $user->getFullName();
        $url = $urlGenerator->generate('bolt_profile_edit');
        $locale = current($request->get('locale'));
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
        if (!filter_var(mb_strlen($user->getFullName()), FILTER_VALIDATE_INT, $usernameValidateOptions)) {
            $this->addFlash('danger', 'user.not_valid_username');
            $hasError = true;
        }

        // Validate password
        if (!empty($newPassword) && mb_strlen($newPassword) < 6) {
            $this->addFlash('danger', 'user.not_valid_password');
            $hasError = true;
        } elseif (!empty($newPassword) && mb_strlen($newPassword) > 6) {
            $user->setPassword($encoder->encodePassword($user, $newPassword));
        }

        // Validate email
        if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'user.not_valid_email');
            $hasError = true;
        }

        if ($hasError) {
            return $this->renderTemplate('users/edit.html.twig', [
                'usertitle' => $userTitle,
                'user' => $user,
            ]);
        }

        $manager->flush();

        $request->getSession()->set('_locale', $locale);

        return new RedirectResponse($url);
    }
}
