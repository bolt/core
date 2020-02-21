<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
use Bolt\Common\Str;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\User;
use Bolt\Utils\UserValidationHandler;
use Bolt\Utils\UserValidator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ProfileController extends TwigAwareController implements BackendZoneInterface
{
    use CsrfTrait;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var UserValidationHandler */
    private $userValidationHandler;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserValidationHandler $userValidationHandler
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->userValidationHandler = $userValidationHandler;
    }

    /**
     * @Route("/profile-edit", methods={"GET"}, name="bolt_profile_edit")
     */
    public function edit(): Response
    {
        $user = $this->getUser();

        return $this->renderTemplate('@bolt/users/profile.html.twig', [
            'display_name' => $user->getDisplayName(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile-edit", methods={"POST"}, name="bolt_profile_edit_post")
     */
    public function save(Request $request): Response
    {
        $this->validateCsrf($request, 'profileedit');

        /** @var User $user */
        $user = $this->getUser();
        $displayName = $user->getDisplayName();
        $url = $this->urlGenerator->generate('bolt_profile_edit');
        $locale = Json::findScalar($request->get('locale'));
        $newPassword = $request->get('password');

        $user->setDisplayName($request->get('displayName'));
        $user->setEmail($request->get('email'));
        $user->setLocale($locale);
        $user->setbackendTheme($request->get('backendTheme'));
        $newPassword = $request->get('password');

        // Set the plain password to check for validation
        if (! empty($newPassword)) {
            $user->setPassword($newPassword);
        }

        $validator = new UserValidator($user);

        if (! $validator->validate()) {
            $this->userValidationHandler->handle($validator);

            $suggestedPassword = $validator->hasPasswordError() ? Str::generatePassword() : null;

            return $this->renderTemplate('@bolt/users/edit.html.twig', [
                'display_name' => $displayName,
                'userEdit' => $user,
                'suggestedPassword' => $suggestedPassword,
            ]);
        }

        // Once validated, encode the password
        if (! empty($newPassword)) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword));
        }

        $this->em->flush();

        $request->getSession()->set('_locale', $locale);

        $this->addFlash('success', 'user.updated_profile');

        return new RedirectResponse($url);
    }
}
