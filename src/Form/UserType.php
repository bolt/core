<?php

declare(strict_types=1);

namespace Bolt\Form;

use Bolt\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // For the full reference of options defined by each form field type
        // see https://symfony.com/doc/current/reference/forms/types.html

        // By default, form fields include the 'required' attribute, which enables
        // the client-side form validation. This means that you can't test the
        // server-side validation errors from the browser. To temporarily disable
        // this validation, set the 'required' attribute to 'false':
        // $builder->add('title', null, ['required' => false, ...]);

        $builder
            ->add('username', TextType::class, [
                'label' => 'label.username',
                'disabled' => true,
            ])
            ->add('displayName', TextType::class, [
                'label' => 'label.displayname',
            ])
            ->add('email', EmailType::class, [
                'label' => 'label.email',
            ])
            ->add('locale', ChoiceType::class, [
                'label' => 'label.locale',
                'choices' => [
                    'English (en)' => 'en',
                    'Nederlands (dutch, nl)' => 'nl',
                    'Español (Spanish, es)' => 'es',
                    'français (French, fr)' => 'fr',
                    'Deutsch (German, de)' => 'de',
                    'Polski (Polish, pl)' => 'pl',
                    'Brasilian Portuguese (Brasilian Portuguese, pt_BR)' => 'pt_BR',
                    'Italiano (Italian, it)' => 'it',
                ],
            ])
            ->add('backendTheme', ChoiceType::class, [
                'label' => 'label.backend_theme',
                'choices' => [
                    'Default Theme' => 'default',
                    'Light Theme' => 'light',
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
