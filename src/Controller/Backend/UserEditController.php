<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
use Bolt\Common\Str;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\User;
use Bolt\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
class UserEditController extends TwigAwareController implements BackendZoneInterface
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
     * @Route("/user-edit/{id}", methods={"GET"}, name="bolt_user_edit", requirements={"id": "\d+"})
     */
    public function edit(?User $user): Response
    {
        $roles = $this->getParameter('security.role_hierarchy.roles');

        if (! $user instanceof User) {
            $user = UserRepository::factory();
            $suggestedPassword = Str::generatePassword();
        } else {
            $suggestedPassword = '';
        }

        return $this->renderTemplate('@bolt/users/edit.html.twig', [
            'display_name' => $user->getDisplayName(),
            'userEdit' => $user,
            'roles' => $roles,
            'suggestedPassword' => $suggestedPassword,
        ]);
    }

    /**
     * @Route("/user-disable/{id}", methods={"POST", "GET"}, name="bolt_user_disable", requirements={"id": "\d+"})
     */
    public function disable(?User $user): Response
    {
        $this->validateCsrf('useredit');

        if ($user->isDisabled()) {
            $user->enable();
            $this->addFlash('success', 'user.enabled_successfully');
        } else {
            $user->disable();
            $this->addFlash('success', 'user.disabled_successfully');
        }

        $this->em->persist($user);
        $this->em->flush();

        $url = $this->urlGenerator->generate('bolt_users');

        return new RedirectResponse($url);
    }

    /**
     * @Route("/user-delete/{id}", methods={"POST", "GET"}, name="bolt_user_delete", requirements={"id": "\d+"})
     */
    public function delete(?User $user): Response
    {
        $this->validateCsrf('useredit');

        $this->em->remove($user);
        $contentArray = $this->getDoctrine()->getManager()->getRepository(\Bolt\Entity\Content::class)->findBy(['author' => $user]);
        foreach ($contentArray as $content) {
            $content->setAuthor(null);
            $this->em->persist($content);
        }

        $mediaArray = $this->getDoctrine()->getManager()->getRepository(\Bolt\Entity\Media::class)->findBy(['author' => $user]);
        foreach ($mediaArray as $media) {
            $media->setAuthor(null);
            $this->em->persist($media);
        }

        $this->em->flush();

        $url = $this->urlGenerator->generate('bolt_users');
        $this->addFlash('success', 'user.updated_profile');

        return new RedirectResponse($url);
    }

    /**
     * @Route("/user-edit/{id}", methods={"POST"}, name="bolt_user_edit_post", requirements={"id": "\d+"})
     */
    public function save(?User $user, ValidatorInterface $validator): Response
    {
        $this->validateCsrf('useredit');

        if (! $user instanceof User) {
            $user = UserRepository::factory();
        }

        $displayName = $user->getDisplayName();
        $locale = Json::findScalar($this->getFromRequest('locale'));
        $roles = (array) Json::findScalar($this->getFromRequest('roles'));

        if (empty($user->getUsername())) {
            $user->setUsername($this->getFromRequest('username'));
        }
        $user->setDisplayName($this->getFromRequest('displayName'));
        $user->setEmail($this->getFromRequest('email'));
        $user->setLocale($locale);
        $user->setRoles($roles);
        $user->setbackendTheme($this->getFromRequest('backendTheme'));
        $newPassword = $this->getFromRequest('password');
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

            return $this->renderTemplate('@bolt/users/edit.html.twig', [
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

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', 'user.updated_profile');

        return $this->redirectToRoute('bolt_users');
    }
}
