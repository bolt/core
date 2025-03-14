<?php

declare(strict_types=1);

namespace Bolt\Form\FieldTypes;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * A Bolt-specific field type to show passwords with a preview toggle button.
 */
class PasswordWithPreviewType extends PasswordType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'password_with_preview';
    }
}
