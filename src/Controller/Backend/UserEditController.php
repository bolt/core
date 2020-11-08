<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Str;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\User;
use Bolt\Enum\UserStatus;
use Bolt\Event\UserEvent;
use Bolt\Form\UserEditType;
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

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        CsrfTokenManagerInterface $csrfTokenManager,
        EventDispatcherInterface $dispatcher
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route("/user-edit/add", methods={"GET","POST"}, name="bolt_user_add")
     */
    public function add(Request $request): Response
    {
        $user = UserRepository::factory();
        $submitted_data = $request->get('user_edit');

        // Always show a strong suggested password, no matter on add or edit page
        $suggestedPassword = Str::generatePassword();

        $event = new UserEvent($user);
        $this->dispatcher->dispatch($event, UserEvent::ON_ADD);

        $roles = array_merge($this->getParameter('security.role_hierarchy.roles'), $event->getRoleOptions()->toArray());

        // These are the variables we have to pass into our FormType so we can build the fields correctly
        $form_data = [
            'suggested_password' => $suggestedPassword,
            'roles' => $roles,
            'require_username' => true,
            'require_password' => true
        ];
        $form = $this->createForm(UserEditType::class, $user, $form_data);

        // ON SUBMIT
        if(!empty($submitted_data)){
            if(!empty($user->getUsername())){
                // Since the username is disabled on edit form we need to set it here so Symfony Forms doesn't throw an error
                $submitted_data['username'] = $user->getUsername();
            }
            // We need to transform to JSON.stringify value for the field "roles" into
            // an array so symfony forms validation works
            $submitted_data['roles'] = json_decode($submitted_data['roles']);
            $form->submit($submitted_data);
        }

        if ($form->isSubmitted() && $form->isValid()){
            return $this->_handleValidFormSubmit($form);
        } else {
            return $this->render('@bolt/users/add.html.twig', [
                'userForm' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/user-edit/{id}", methods={"GET","POST"}, name="bolt_user_edit", requirements={"id": "\d+"})
     */
    public function edit(?User $user, Request $request): Response
    {
        $submitted_data = $request->get('user_edit');

        // Always show a strong suggested password, no matter on add or edit page
        $suggestedPassword = Str::generatePassword();

        $event = new UserEvent($user);
        $this->dispatcher->dispatch($event, UserEvent::ON_EDIT);

        $roles = array_merge($this->getParameter('security.role_hierarchy.roles'), $event->getRoleOptions()->toArray());

        // We don't require the user to set the password again on the "user edit" form
        // If it is otherwise set use the given password normally
        $require_password = false;
        if(!empty($submitted_data['plainPassword'])){
            $require_password = true;
        }

        // These are the variables we have to pass into our FormType so we can build the fields correctly
        $form_data = [
            'suggested_password' => $suggestedPassword,
            'roles' => $roles,
            'require_username' => false,
            'require_password' => $require_password
        ];
        $form = $this->createForm(UserEditType::class, $user, $form_data);

        // ON SUBMIT
        if(!empty($submitted_data)){
            // Since the username is disabled on edit form we need to set it here so Symfony Forms doesn't throw an error
            $submitted_data['username'] = $user->getUsername();

            // We need to transform to JSON.stringify value for the field "roles" into
            // an array so symfony forms validation works
            $submitted_data['roles'] = json_decode($submitted_data['roles']);
            $form->submit($submitted_data);
        }

        if ($form->isSubmitted() && $form->isValid()){
            return $this->_handleValidFormSubmit($form);
        } else {
            return $this->render('@bolt/users/edit.html.twig', [
                'userForm' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/user-status/{id}", methods={"POST", "GET"}, name="bolt_user_update_status", requirements={"id": "\d+"})
     */
    public function status(?User $user): Response
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
     * This function is called by add and edit function if given form was submitted and validated correctly
     * Here the User Object will be persisted to the DB and the user will be redirected to the overview page
     *
     * @param FormInterface $form
     * @return RedirectResponse
     */
    private function _handleValidFormSubmit(FormInterface $form) {
        // Get the adjusted User Entity from the form
        /** @var User $user */
        $user = $form->getData();

        // Once validated, encode the password
        if ($user->getPlainPassword()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
        }

        // Save the new user data into the DB
        $this->em->persist($user);
        $this->em->flush();

        $event = new UserEvent($user);
        $this->dispatcher->dispatch($event, UserEvent::ON_POST_SAVE);

        $this->addFlash('success', 'user.updated_profile');
        return $this->redirectToRoute('bolt_users');
    }
}
