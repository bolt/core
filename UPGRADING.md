# From Bolt 5.2

## Drop knplabs/doctrine-behaviors dependency

The `knplabs/doctrine-behaviors` package has been removed from the Bolt core.

The translation behavior has been integrated.

Namespace changes:

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
