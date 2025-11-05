# From Bolt 5.2 to 6.0

## Dropped Bolt provided migrations

As migrations heavily depend on the used database and were incomplete to begin with, we have dropped database migrations from this bundle. Instead, we will be offering a migration path or example migrations in these notes.

In order to upgrade, you will need to squash your existing migrations. You can follow this guide, which is based on https://jolicode.com/blog/a-new-way-to-squash-your-doctrine-migrations.

1. Remove any migration you made yourself
2. Validate that your database is in sync with the code by running `bin/console doctrine:migrations:diff`. If any migrations are generated, inspect them for correctness (changes to the used Doctrine version might have caused changes) and keep the files.
3. Run `bin/console doctrine:migrations:dump-schema`
4. Prepend the following to the newly generated migration
   ```php
   if ($this->sm->tablesExist(['bolt_field'])) {
       $this->addSql('DELETE FROM migration_versions');
       return;
   }
   // All other code of the migration
   ```
5. Rename the migration file to `Version00000000000000.php` and rename the class too. This ensures that it is run first and that migrations generated in step 3 are still being executed to fix your database state after upgrading.

This should work on your production environment as well when you are running the `doctrine:migrations:migrate` on deployment, but as always we recommend to make a backup before trying the upgrade.

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
