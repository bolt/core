<?php

namespace Bolt\Form;

use Bolt\Entity\User;
use Bolt\Enum\UserStatus;
use Bolt\Utils\LocaleHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class UserEditType extends AbstractType
{

    /** @var TranslatorInterface */
    private $translator;

    /** @var LocaleHelper */
    private $localeHelper;

    /** @var Environment */
    private $twig;

    public function __construct(TranslatorInterface $translator, LocaleHelper $localeHelper, Environment $twig)
    {
        $this->translator = $translator;
        $this->localeHelper = $localeHelper;
        $this->twig = $twig;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Create custom role options array
        $roleOptions = [];
        $custom_roles = $options['roles'];
        foreach($custom_roles as $roleName => $roleHierarchy):
            // For some reason these select arrays are built like
            // array-key => Label
            // array-value => Key which is used to save in the DB (and used for validation)
            $label = strtolower(implode(', ', $roleHierarchy));
            $roleOptions[$label] = $roleName;
        endforeach;

        // Create custom location options array
        $locationOptions = [];
        $custom_locations = $this->localeHelper->getLocales($this->twig, null, true)->all();
        foreach($custom_locations as $location):
            $data = $location->all();
            // Same strange array structure as above
            $description = sprintf('%s %s (%s, %s)', $data['emoji'], $data['name'], $data['localizedname'], $data['code']);
            $locationOptions[$description] = $data['code'];
        endforeach;

        $builder
            ->add('username', TextType::class, [
                'required' => $options['require_username']
            ])
            ->add('displayName', TextType::class)
            ->add('plainPassword', PasswordType::class, [
                // We need that, otherwise the suggested password won't be auto filled in
                'always_empty' => false,
                'attr' => [
                    'suggested_password' => $options['suggested_password']
                ],
                'required' => $options['require_password'],
                'empty_data' => ''
            ])
            ->add('email', EmailType::class)
            ->add('locale', ChoiceType::class, [
                'choices' => $locationOptions
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => $roleOptions,
                'multiple' => true,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => UserStatus::all()
            ]);
//            ->add('lastseenAt')
//            ->add('lastIp')
//            ->add('backendTheme')
//            ->add('userAuthToken')
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'suggested_password' => '',
            'roles' => '',
            'require_username' => '',
            'require_password' => '',
            'validation_groups' => function (FormInterface $form) {
                /** @var User $entity */
                $entity = $form->getData();

                $validation_group = "edit_user";
                if($entity->isNewUser()){
                    $validation_group = "add_user";
                } else if($entity->getPlainPassword() === '') {
                    $validation_group = "edit_user_without_pw";
                }

                return $validation_group;
            },
        ]);
    }
}
