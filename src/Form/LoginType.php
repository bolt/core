<?php

declare(strict_types=1);

namespace Bolt\Form;

use Bolt\Form\FieldTypes\PasswordWithPreviewType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginType extends AbstractType
{
    /** @var AuthenticationUtils */
    private $authenticationUtils;

    /** @var TranslatorInterface */
    private $translator;

    /** @var int */
    private $rememberLifetime;

    public function __construct(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator, int $rememberLifetime = 2592000)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->translator = $translator;

        // Defaults to 2592000, 30 days in seconds
        $this->rememberLifetime = $rememberLifetime;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'label.username_or_email',
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('form.empty_username_email'),
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'placeholder.username_or_email',
                ],
                'data' => $this->authenticationUtils->getLastUsername(),
            ])
            ->add('password', PasswordWithPreviewType::class, [
                'label' => 'label.password',
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('form.empty_password'),
                    ]),
                ],
                // do not show the * red star
                'required' => false,
                'attr' => [
                    'placeholder' => 'placeholder.password',
                ],
            ]);

        if ($this->rememberLifetime > 0) {
            $builder->add('remember_me', CheckboxType::class, [
                'label' => 'label.remembermeduration',
                'label_translation_parameters' => [
                    '%duration%' => (int) sprintf('%0.1f', $this->rememberLifetime / 3600 / 24),
                ],
                'required' => false,
                'attr' => [
                    'checked' => 'checked',
                ],
            ]);
        } else {
            $builder->add('remember_me', HiddenType::class);
        }
    }

    // https://symfony.com/doc/current/security/csrf.html#csrf-protection-in-symfony-forms
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id' => 'login_csrf_token',
        ]);
    }
}
