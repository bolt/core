<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Config;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\User;
use Bolt\Form\ChangePasswordFormType;
use Bolt\Form\ResetPasswordRequestFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @Route("/reset-password")
 */
class ResetPasswordController extends TwigAwareController
{
    use ResetPasswordControllerTrait;

    /** @var ResetPasswordHelperInterface */
    private $resetPasswordHelper;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, Config $config, TranslatorInterface $translator)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->config = $config;
        $this->translator = $translator;
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("", name="bolt_forgot_password_request")
     */
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer
            );
        }

        $templates = $this->templateChooser->forResetPasswordRequest();

        return $this->render($templates, [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/check-email", name="bolt_check_email")
     */
    public function checkEmail(): Response
    {
        // We prevent users from directly accessing this page
        if (! $this->canCheckEmail()) {
            return $this->redirectToRoute('bolt_forgot_password_request');
        }

        $templates = $this->templateChooser->forResetPasswordCheckEmail();

        return $this->render($templates, [
            'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="bolt_reset_password")
     */
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, ?string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('bolt_reset_password');
        }

        $token = $this->getTokenFromSession();
        if ($token === null) {
            throw $this->createNotFoundException($this->translator->trans('reset_password.no_token'));
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                $this->translator->trans('reset_password.problem_with_request'),
                $this->translator->trans($e->getReason())
            ));

            return $this->redirectToRoute('bolt_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode the plain password, and set it.
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            // Added an additional flash message to show if password reset was successful
            $this->addFlash('reset_password_success', $this->translator->trans('reset_password.reset_successful'));

            return $this->redirectToRoute('bolt_login');
        }

        $templates = $this->templateChooser->forResetPasswordReset();

        return $this->render($templates, [
            'resetForm' => $form->createView(),
        ]);
    }

    protected function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Marks that you are allowed to see the bolt_check_email page.
        $this->setCanCheckEmailInSession();

        // Do not reveal whether a user account was found or not.
        if (! $user) {
            return $this->redirectToRoute('bolt_check_email');
        }

        // Global config/bolt/config.yml file.
        $config = $this->config->get('general/reset_password_settings');

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'bolt_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.

            if ($config['show_already_requested_password_notice']) {
                $this->addFlash('reset_password_error', sprintf(
                    $this->translator->trans('reset_password.problem_with_request'),
                    $this->translator->trans($e->getReason())
                ));
            }

            return $this->redirectToRoute('bolt_check_email');
        }

        $email = $this->buildResetEmail($config, $user, $resetToken);

        $mailer->send($email);

        return $this->redirectToRoute('bolt_check_email');
    }

    protected function buildResetEmail($config, $user, $resetToken): Email
    {
        return (new TemplatedEmail())
            ->from(new Address($config['mail_from'], $config['mail_name']))
            ->to($user->getEmail())
            ->subject($config['mail_subject'])
            ->htmlTemplate($config['mail_template'])
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ]);
    }
}
