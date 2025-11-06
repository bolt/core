# From Bolt 5.2 to 6.0

## Replaced `tightenco/collect` with `illuminate/collections`

If you were using classes from `Tightenco\Collect\Support`, you should replace them with `Illuminate\Support\` or install the deprecated `tightenco/collect` library yourself.

## Dropped `knplabs/doctrine-behaviors` dependency

The `knplabs/doctrine-behaviors` package has been removed from the Bolt core, but the translation behavior that was used by Bolt core has been integrated.

The following classes have replaced:

| Old class                                                            | New class                                      |
|----------------------------------------------------------------------|------------------------------------------------|
| Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface          | Bolt\Entity\Translatable\TranslatableInterface |
| Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface           | Bolt\Entity\Translatable\TranslationInterface  |
| Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait           | Bolt\Entity\Translatable\BoltTranslatableTrait |
| Knp\DoctrineBehaviors\Model\Translatable\TranslatableMethodsTrait    | No longer exists                               |
| Knp\DoctrineBehaviors\Model\Translatable\TranslatablePropertiesTrait | No longer exists                               |
| Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait            | Bolt\Entity\Translatable\BoltTranslationTrait  |
| Knp\DoctrineBehaviors\Model\Translatable\TranslationMethodsTrait     | No longer exists                               |
| Knp\DoctrineBehaviors\Model\Translatable\TranslationPropertiesTrait  | No longer exists                               |
| Knp\DoctrineBehaviors\EventSubscriber\TranslatableEventSubscriber    | Bolt\Event\Listener\TranslatableListener       |
| Knp\DoctrineBehaviors\Exception\TranslatableException                | No longer exists                               |
| Knp\DoctrineBehaviors\Provider\LocaleProvider                        | Bolt\Locale\LocaleProvider                     |
| Knp\DoctrineBehaviors\Contract\Provider\LocaleProviderInterface      | Bolt\Locale\LocaleProviderInterface            |

Check if this line has been removed from your `config/bundles.php` file:

```php
Knp\DoctrineBehaviors\DoctrineBehaviorsBundle::class => ['all' => true],
```
