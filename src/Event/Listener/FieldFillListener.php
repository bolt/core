<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Entity\Field;
use Bolt\Entity\Field\RawPersistable;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\FieldTranslation;
use Bolt\Utils\Sanitiser;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Twig\Markup;

class FieldFillListener
{
    /** @var Sanitiser */
    private $sanitiser;

    public function __construct(Sanitiser $sanitiser)
    {
        $this->sanitiser = $sanitiser;
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof FieldTranslation && $entity->getTranslatable() instanceof FieldInterface) {
            /** @var Field $field */
            $field = $entity->getTranslatable();

            if (! $field instanceof RawPersistable && $field->getDefinition()->get('sanitise', true)) {
                $value = $this->clean($field->getParsedValue());
                $field->setValue($value);
            }
        }
    }

    private function clean($value): array
    {
        if (! is_iterable($value)) {
            $value = [$value];
        }

        $result = [];

        foreach ($value as $key => $v) {
            if ($v instanceof Markup) {
                $v = self::trimZeroWidthWhitespace((string) $v);
                // todo: Figure out how to preserve original encoding
                $v = new Markup($this->sanitiser->clean($v), 'UTF-8');
            } elseif (is_string($v)) {
                $v = self::trimZeroWidthWhitespace($v);
                $v = $this->sanitiser->clean($v);
            }

            $result[$key] = $v;
        }

        return $result;
    }

    /**
     * Remove the 'zero width space' from `{{` and `}}`, added in the editor.
     */
    public static function trimZeroWidthWhitespace(string $string): string
    {
        return preg_replace('/([{}])[\x{200B}-\x{200D}\x{FEFF}]([{}])/u', '$1$2', $string);
    }

    /**
     * @deprecated since Bolt 5.1.9
     */
    public function postLoad(LifecycleEventArgs $args): void
    {
    }
}
