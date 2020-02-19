<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
use Bolt\Common\Str;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\User;
use Bolt\Repository\UserRepository;
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
class UserEditController extends TwigAwareController implements BackendZoneInterface
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
    public function disable(?User $user, Request $request): Response
    {
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
    public function delete(?User $user, Request $request): Response
    {
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
    public function save(?User $user, Request $request): Response
    {
        $this->validateCsrf($request, 'useredit');

        if (! $user instanceof User) {
            $user = UserRepository::factory();
        }

        $displayName = $user->getDisplayName();
        $url = $this->urlGenerator->generate('bolt_users');
        $locale = Json::findScalar($request->get('locale'));
        $roles = (array) Json::findScalar($request->get('roles'));

        if (empty($user->getUsername())) {
            $user->setUsername($request->get('username'));
        }
        $user->setDisplayName($request->get('displayName'));
        $user->setEmail($request->get('email'));
        $user->setLocale($locale);
        $user->setRoles($roles);
        $user->setbackendTheme($request->get('backendTheme'));
        $newPassword = $request->get('password');
        empty($newPassword) ?: $user->setPassword($newPassword);

        $validator = new UserValidator($user);

        if(! $validator->validate()) {
            $this->userValidationHandler->handle($validator);
            return $this->renderTemplate('@bolt/users/edit.html.twig', [
                'display_name' => $displayName,
                'userEdit' => $user,
            ]);
        }

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', 'user.updated_profile');

        return new RedirectResponse($url);
    }
}
