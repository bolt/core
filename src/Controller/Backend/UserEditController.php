<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
use Bolt\Common\Str;
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
class UserEditController extends TwigAwareController implements BackendZone
{
    use CsrfTrait;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var ObjectManager */
    private $em;

    /** @var UserPasswordEncoderInterface */
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
     * @Route("/user-edit/{id}", methods={"GET"}, name="bolt_user_edit", requirements={"id": "\d+"})
     */
    public function edit(?User $user): Response
    {
        $roles = $this->getParameter('security.role_hierarchy.roles');

        if (! $user instanceof User) {
            $user = User::factory();
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
        if($user->isDisabled()){
           $user->enable();
        $this->addFlash('success', 'user.enabled_successfully');
        }else{
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
        #$this->validateCsrf($request, 'useredit');

        $this->em->remove($user);
        $contentArray = $this->getDoctrine()->getManager()->getRepository('Bolt\Entity\Content')->findBy(['author' => $user]);
        foreach($contentArray as $content){
            $content->setAuthor(null);
            $this->em->persist($content);
        }

        $mediaArray = $this->getDoctrine()->getManager()->getRepository('Bolt\Entity\Media')->findBy(['author'=> $user]);
        foreach($mediaArray as $media){
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
            $user = User::factory();
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

        if ($this->validateUser($user, $newPassword) === false) {
            return $this->renderTemplate('@bolt/users/edit.html.twig', [
                'display_name' => $displayName,
                'userEdit' => $user,
            ]);
        }

        if (! empty($newPassword)) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword));
        }

        $this->em->persist($user);
        $this->em->flush();

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
