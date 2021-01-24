<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Str;
use Bolt\Configuration\Config;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\User;
use Bolt\Enum\UserStatus;
use Bolt\Event\UserEvent;
use Bolt\Form\UserType;
use Bolt\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UserEditController extends TwigAwareController implements BackendZoneInterface
{
    use CsrfTrait;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    protected $defaultLocale;

    private $assignableRoles;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        CsrfTokenManagerInterface $csrfTokenManager,
        EventDispatcherInterface $dispatcher,
        Config $config,
        string $defaultLocale
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->dispatcher = $dispatcher;
        $this->defaultLocale = $defaultLocale;
        $this->assignableRoles = $config->get('permissions/assignable_roles')->all();
    }

    /**
     * @Route("/user-edit/add", methods={"GET","POST"}, name="bolt_user_add")
     * @Security("is_granted('user:add')")
     */
    public function add(Request $request): Response
    {
        $user = UserRepository::factory();
        $submitted_data = $request->request->get('user');

        $event = new UserEvent($user);
        $this->dispatcher->dispatch($event, UserEvent::ON_ADD);
        $roles = $this->_getPossibleRolesForForm();

        // These are the variables we have to pass into our FormType so we can build the fields correctly
        $form_data = [
            'suggested_password' => Str::generatePassword(),
            'roles' => $roles,
            'require_username' => true,
            'require_password' => true,
            'default_locale' => $this->defaultLocale,
            'is_profile_edit' => false,
        ];
        $form = $this->createForm(UserType::class, $user, $form_data);

        // ON SUBMIT
        if (! empty($submitted_data)) {
            // We need to transform to JSON.stringify value for the field "roles" into
            // an array so symfony forms validation works
            $submitted_data['roles'] = json_decode($submitted_data['roles']);

            $submitted_data['locale'] = json_decode($submitted_data['locale'])[0];
            $submitted_data['status'] = json_decode($submitted_data['status'])[0];

            // Transform media array to keep only filepath
            $submitted_data['avatar'] = $submitted_data['avatar']['filename'];

            $form->submit($submitted_data);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->_handleValidFormSubmit($form);

            return $this->redirectToRoute('bolt_users');
        }

        return $this->render('@bolt/users/add.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile-edit", methods={"GET","POST"}, name="bolt_profile_edit")
     *
     * @Security("is_granted('editprofile')")
     */
    public function editProfile(Request $request): Response
    {
        $submitted_data = $request->request->get('user');
        /** @var User $user */
        $user = $this->getUser();

        return $this->handleEdit(true, $user, $submitted_data);
    }

    /**
     * @Route("/user-edit/{id}", methods={"GET","POST"}, name="bolt_user_edit", requirements={"id": "\d+"})
     *
     * @Security("is_granted('user:edit')")
     */
    public function edit(User $user, Request $request): Response
    {
        $submitted_data = $request->request->get('user');

        return $this->handleEdit(false, $user, $submitted_data);
    }

    /**
     * @Route("/user-status/{id}", methods={"POST", "GET"}, name="bolt_user_update_status", requirements={"id": "\d+"})
     * @Security("is_granted('user:status')") -- first check, more detailed checks in method
     */
    public function status(User $user): Response
    {
        $this->validateCsrf('useredit');

        $newStatus = $this->request->get('status', UserStatus::DISABLED);

        $user->setStatus($newStatus);
        $this->addFlash('success', 'user.updated_successfully');

        $this->em->persist($user);
        $this->em->flush();

        $url = $this->urlGenerator->generate('bolt_users');

        return new RedirectResponse($url);
    }

    /**
     * @Route("/user-delete/{id}", methods={"POST", "GET"}, name="bolt_user_delete", requirements={"id": "\d+"})
     * @Security("is_granted('user:delete')")
     */
    public function delete(User $user): Response
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
     * This function is called by add and edit function if given form was submitted and validated correctly.
     * Here the User Object will be persisted to the DB. A security exception will be raised if the roles
     * for the user being saved are not allowed for the current logged user.
     */
    private function _handleValidFormSubmit(FormInterface $form): void
    {
        // Get the adjusted User Entity from the form
        /** @var User $user */
        $user = $form->getData();

        // Once validated, encode the password
        if ($user->getPlainPassword()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
        }

        $event = new UserEvent($user);
        $this->dispatcher->dispatch($event, UserEvent::ON_PRE_SAVE);

        // Save the new user data into the DB
        $this->em->persist($user);
        $this->em->flush();

        $event = new UserEvent($user);
        $this->dispatcher->dispatch($event, UserEvent::ON_POST_SAVE);

        $this->addFlash('success', 'user.updated_profile');
    }

    private function _getPossibleRolesForForm(): array
    {
        $result = [];
        $assignableRoles = $this->assignableRoles;

        // convert into array for form
        foreach ($assignableRoles as $assignableRole) {
            $result[$assignableRole] = $assignableRole;
        }

        return $result;
    }

    /**
     * @return RedirectResponse|Response
     */
    private function handleEdit(bool $is_profile_edit, User $user, $submitted_data)
    {
        $redirectRouteAfterSubmit = $is_profile_edit ? 'bolt_profile_edit' : 'bolt_users';
        $event = new UserEvent($user);
        $this->dispatcher->dispatch($event, UserEvent::ON_EDIT);

        $roles = $this->_getPossibleRolesForForm();

        // We don't require the user to set the password again on the "user edit" form
        // If it is otherwise set use the given password normally
        $require_password = false;
        if (! empty($submitted_data['plainPassword'])) {
            $require_password = true;
        }

        // These are the variables we have to pass into our FormType so we can build the fields correctly
        $form_data = [
            'suggested_password' => Str::generatePassword(),
            'roles' => $roles,
            'require_username' => false,
            'require_password' => $require_password,
            'default_locale' => $this->defaultLocale,
            'is_profile_edit' => $is_profile_edit,
        ];
        $form = $this->createForm(UserType::class, $user, $form_data);

        // ON SUBMIT
        if (! empty($submitted_data)) {
            // Since the username is disabled on edit form we need to set it here so Symfony Forms doesn't throw an error
            $submitted_data['username'] = $user->getUsername();

            $submitted_data['locale'] = json_decode($submitted_data['locale'])[0];

            // Status is not available for profile edit on non admin users
            if (! empty($submitted_data['status'])) {
                $submitted_data['status'] = json_decode($submitted_data['status'])[0];
            }

            // Roles is not available for profile edit on non admin users
            if (! empty($submitted_data['roles'])) {
                // We need to transform to JSON.stringify value for the field "roles" into
                // an array so symfony forms validation works
                $submitted_data['roles'] = json_decode($submitted_data['roles']);
            }

            // Transform media array to keep only filepath
            $submitted_data['avatar'] = $submitted_data['avatar']['filename'];

            $form->submit($submitted_data);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->_handleValidFormSubmit($form);

            return $this->redirectToRoute($redirectRouteAfterSubmit);
        }

        return $this->render('@bolt/users/edit.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }
}
