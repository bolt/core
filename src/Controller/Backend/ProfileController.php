<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
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
 * Class ProfileController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class ProfileController extends BaseController
{
    /**
     * @Route("/profile-edit", methods={"GET"}, name="bolt_profile_edit")
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function profileEdit(Request $request): Response
    {
        $user = $this->getUser();

        return $this->renderTemplate('users/edit.html.twig', [
            'display_name' => $user->getDisplayName(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile-edit", methods={"POST"}, name="bolt_profile_edit_post")
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function profileEditPost(Request $request, UrlGeneratorInterface $urlGenerator, ObjectManager $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
        $displayName = $user->getDisplayName();
        $url = $urlGenerator->generate('bolt_profile_edit');
        $locale = Json::findScalar($request->get('locale'));
        $newPassword = $request->get('password');

        $user->setDisplayName($request->get('displayName'));
        $user->setEmail($request->get('email'));
        $user->setLocale($locale);
        $user->setbackendTheme($request->get('user')['backendTheme']);

        $hasError = false;

        $usernameValidateOptions = [
            'options' => [
                'min_range' => 1,
            ],
        ];

        // @todo Validation must be moved to a separate UserValidator

        // Validate username
        if (! filter_var(mb_strlen($user->getDisplayName()), FILTER_VALIDATE_INT, $usernameValidateOptions)) {
            $this->addFlash('danger', 'user.not_valid_username');
            $hasError = true;
        }

        // Validate password
        if (! empty($newPassword) && mb_strlen($newPassword) < 6) {
            $this->addFlash('danger', 'user.not_valid_password');
            $hasError = true;
        } elseif (! empty($newPassword) && mb_strlen($newPassword) > 6) {
            $user->setPassword($encoder->encodePassword($user, $newPassword));
        }

        // Validate email
        if (! filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'user.not_valid_email');
            $hasError = true;
        }

        if ($hasError) {
            return $this->renderTemplate('users/edit.html.twig', [
                'display_name' => $displayName,
                'user' => $user,
            ]);
        }

        $manager->flush();

        $request->getSession()->set('_locale', $locale);

        return new RedirectResponse($url);
    }
}
