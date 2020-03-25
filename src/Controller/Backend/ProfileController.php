<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
use Bolt\Common\Str;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $em,
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
    public function save(Request $request, ValidatorInterface $validator): Response
    {
        $this->validateCsrf($request, 'profileedit');

        /** @var User $user */
        $user = $this->getUser();
        $displayName = $user->getDisplayName();
        $locale = Json::findScalar($request->get('locale'));

        $user->setDisplayName($request->get('displayName'));
        $user->setEmail($request->get('email'));
        $user->setLocale($locale);
        $user->setbackendTheme($request->get('backendTheme'));
        $newPassword = $request->get('password');

        // Set the plain password to check for validation
        if (! empty($newPassword)) {
            $user->setPlainPassword($newPassword);
        }

        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            $hasPasswordError = false;

            /** @var ConstraintViolationInterface $error */
            foreach ($errors as $error) {
                $this->addFlash('danger', $error->getMessage());

                if ($error->getPropertyPath() === 'plainPassword') {
                    $hasPasswordError = true;
                }
            }

            $suggestedPassword = $hasPasswordError ? Str::generatePassword() : null;

            return $this->renderTemplate('@bolt/users/profile.html.twig', [
                'display_name' => $displayName,
                'userEdit' => $user,
                'suggestedPassword' => $suggestedPassword,
            ]);
        }

        // Once validated, encode the password
        if ($user->getPlainPassword()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
        }

        $this->em->flush();

        $request->getSession()->set('_locale', $locale);

        $this->addFlash('success', 'user.updated_profile');

        return $this->redirectToRoute('bolt_profile_edit');
    }
}
