<?php

    declare(strict_types=1);

namespace Bolt\Form;

    use Cocur\Slugify\Slugify;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Contracts\Translation\TranslatorInterface;

    class LoginType extends AbstractType
    {
        /** @var AuthenticationUtils  */
        private $authenticationUtils;

        /** @var TranslatorInterface  */
        private $translator;

        public function __construct(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator)
        {
            $this->authenticationUtils = $authenticationUtils;
            $this->translator = $translator;
        }

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $slugify = new Slugify();

            // last username entered by the user (if any)
            $last_username = $slugify->slugify($this->authenticationUtils->getLastUsername());

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
                    'data' => $last_username,
                    'required' => false
                ])
                ->add('password', PasswordType::class, [
                    'label' => 'label.password',
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->translator->trans('form.empty_password'),
                        ]),
                    ],
                    'attr' => [
                        'placeholder' => 'placeholder.password',
                    ],
                    'required' => false
                ]);
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
