# From Bolt 5.2 to 6.0

## Replaced `tightenco/collect` with `illuminate/collections`

If you were using classes from `Tightenco\Collect\Support`, you should replace them with `Illuminate\Support\` or install the deprecated `tightenco/collect` library yourself.

## Dropped `knplabs/doctrine-behaviors` dependency

The `knplabs/doctrine-behaviors` package has been removed from the Bolt core, but the translation behavior that was used by Bolt core has been integrated.

The following classes have replaced:

| Old class                                                            | New class                                            |
|----------------------------------------------------------------------|------------------------------------------------------|
| Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface          | Bolt\Entity\TranslatableInterface                    |
| Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface           | Bolt\Entity\TranslationInterface                     |
| Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait           | Use two trait below                                  |
| Knp\DoctrineBehaviors\Model\Translatable\TranslatableMethodsTrait    | Bolt\Entity\Translatable\TranslatableMethodsTrait    |
| Knp\DoctrineBehaviors\Model\Translatable\TranslatablePropertiesTrait | Bolt\Entity\Translatable\TranslatablePropertiesTrait |
| Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait            | Use two trait below                                  |
| Knp\DoctrineBehaviors\Model\Translatable\TranslationMethodsTrait     | Bolt\Entity\Translatable\TranslationMethodsTrait     |
| Knp\DoctrineBehaviors\Model\Translatable\TranslationPropertiesTrait  | Bolt\Entity\Translatable\TranslationPropertiesTrait  |
| Knp\DoctrineBehaviors\EventSubscriber\TranslatableEventSubscriber    | Bolt\Event\Listener\TranslatableListener             |
| Knp\DoctrineBehaviors\Exception\TranslatableException                | Bolt\Exception\TranslatableException                 |
| Knp\DoctrineBehaviors\Provider\LocaleProvider                        | Bolt\Locale\LocaleProvider                           |
| Knp\DoctrineBehaviors\Contract\Provider\LocaleProviderInterface      | Bolt\Locale\LocaleProviderInterface                  |
