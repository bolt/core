<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
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
class ProfileController extends TwigAwareController implements BackendZone
{
    use CsrfTrait;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ObjectManager $em,
        UserPasswordEncoderInterface $passwordEncoder,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
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

        $user = $this->getUser();
        $displayName = $user->getDisplayName();
        $url = $this->urlGenerator->generate('bolt_profile_edit');
        $locale = Json::findScalar($request->get('locale'));
        $newPassword = $request->get('password');

        $user->setDisplayName($request->get('displayName'));
        $user->setEmail($request->get('email'));
        $user->setLocale($locale);
        $user->setbackendTheme($request->get('backendTheme'));

        if ($this->validateUser($user, $newPassword) === false) {
            return $this->renderTemplate('@bolt/users/edit.html.twig', [
                'display_name' => $displayName,
                'user' => $user,
            ]);
        }

        if ($newPassword !== null) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword));
        }

        $this->em->flush();

        $request->getSession()->set('_locale', $locale);

        $this->addFlash('success', 'user.updated_profile');

        return new RedirectResponse($url);
    }

    private function validateUser(User $user, ?string $newPassword): bool
    {
        // @todo Validation should be moved to a separate UserValidator

        $usernameValidateOptions = [
            'options' => [
                'min_range' => 1,
            ],
        ];

        // Validate username
        if (! filter_var(mb_strlen($user->getDisplayName()), FILTER_VALIDATE_INT, $usernameValidateOptions)) {
            $this->addFlash('danger', 'user.not_valid_username');
            return false;
        }

        // Validate email
        if (! filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'user.not_valid_email');
            return false;
        }

        // Validate password
        if (! empty($newPassword) && mb_strlen($newPassword) < 6) {
            $this->addFlash('danger', 'user.not_valid_password');
            return false;
        }

        return true;
    }
}
