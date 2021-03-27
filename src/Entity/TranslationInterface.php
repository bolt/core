<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface as KnpTranslationInterface;

interface TranslationInterface extends KnpTranslationInterface
{
}

/*
 * The following prevents a 'Class "Knp\DoctrineBehaviors\Model\Translatable\TranslationInterface" does not exist'-Exception
 * See screenshot: https://github.com/bolt/core/pull/2496#issuecomment-808725120
 */
class_alias(TranslationInterface::class, 'Knp\DoctrineBehaviors\Model\Translatable\TranslationInterface');
