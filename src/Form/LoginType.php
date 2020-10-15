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

    class LoginType extends AbstractType
    {
        public $authenticationUtils;

        public function __construct(AuthenticationUtils $authenticationUtils)
        {
            $this->authenticationUtils = $authenticationUtils;
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
                            'message' => 'Please enter your username or email',
                        ]),
                    ],
                    'attr' => [
                        'placeholder' => 'placeholder.username_or_email',
                    ],
                    'data' => $last_username,
                ])
                ->add('password', PasswordType::class, [
                    'label' => 'label.password',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter your password',
                        ]),
                    ],
                    'attr' => [
                        'placeholder' => 'placeholder.password',
                    ],
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
