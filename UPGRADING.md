# From Bolt 5.2 to 6.0

## Symfony upgrade

This release upgrades to Symfony 6.4, so you will need to verify your own implementation. The `sensio/framework-extra-bundle` has been dropped as dependency, so migrate those usages as well, or include the dependency yourself (not recommended).

You will want to update your `bin/console` and `public/index.php` files to leverage the `symfony/runtime` component.

## Configuration upgrades

Normally we try to migrate your configuration files when possible (and we still do this for a lot of files), but it might fail for you. You can inspect the files in `yaml-migrations` with since `6.0.0` to learn what needs to be done. Afterwards, update your `checkpoint.txt` in that same folder to hold `6.0.0`.

Next to that, Symfony will be dropping dev/test/prod folders in favour of the `when@dev` notation. While we made the same migration, we will not be migrating this for you so you will need to do that yourself. We strongly advise to check all your configuration in your configuration folder to check whether you are missing any configuration updates from our end, and combine that correctly with your custom configuration. As Symfony has also gotten a large upgrade, checking the configuration is needed anyways.

## Dropped Bolt provided migrations

As migrations heavily depend on the used database and were incomplete to begin with, we have dropped database migrations from this application. Instead, we will be offering a migration path or example migrations in these notes.

In order to upgrade, you will need to squash your existing migrations. You can follow this guide, which is based on https://jolicode.com/blog/a-new-way-to-squash-your-doctrine-migrations. Also, please make sure that you have your own migration configuration looking like the following in `config/packages/doctrine_migrations.yml`:
```yaml
doctrine_migrations:
  migrations_paths:
    DoctrineMigrations: '%kernel.project_dir%/migrations'
  enable_profiler: '%kernel.debug%'
```

1. Remove any migration you made yourself
2. Validate that your database is in sync with the code by running `bin/console doctrine:migrations:diff`. If any migrations are generated, inspect them for correctness (changes to the used Doctrine version will have caused changes) and keep the files. The following migrations are to be expected from Bolt itself (when using MySQL):
   - The `bolt_log` table has four columns (`context`, `extra`, `user` and `location`) which will be changed from `longtext` to `json`, which requires action on your part. You can either truncate the table on beforehand (which removes all existing logs), or convert the data that is in those columns from a PHP-serialized array to the new JSON structure. For the latter scenario you can use the following migration (tested with MariaDB only):
     ```php
     public function preUp(Schema $schema): void
     {
         \Bolt\Doctrine\Migrations\ArrayToJsonMigrator::migrateUp($this->connection, 'bolt_log', 'context', $this->write(...));
         \Bolt\Doctrine\Migrations\ArrayToJsonMigrator::migrateUp($this->connection, 'bolt_log', 'extra', $this->write(...));
         \Bolt\Doctrine\Migrations\ArrayToJsonMigrator::migrateUp($this->connection, 'bolt_log', 'user', $this->write(...));
         \Bolt\Doctrine\Migrations\ArrayToJsonMigrator::migrateUp($this->connection, 'bolt_log', 'location', $this->write(...));
     }

     public function up(Schema $schema): void
     {
         $this->addSql('ALTER TABLE bolt_log
           CHANGE context context JSON DEFAULT NULL COMMENT \'(DC2Type:json)\',
           CHANGE extra extra JSON DEFAULT NULL COMMENT \'(DC2Type:json)\',
           CHANGE user `user` JSON DEFAULT NULL COMMENT \'(DC2Type:json)\',
           CHANGE location location JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
     }

     public function down(Schema $schema): void
     {
         $this->addSql('ALTER TABLE bolt_log
           CHANGE context context LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\',
           CHANGE extra extra LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\',
           CHANGE `user` user LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\',
           CHANGE location location LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
     }

     public function postDown(Schema $schema): void
     {
         \Bolt\Doctrine\Migrations\ArrayToJsonMigrator::migrateDown($this->connection, 'bolt_log', 'context', $this->write(...));
         \Bolt\Doctrine\Migrations\ArrayToJsonMigrator::migrateDown($this->connection, 'bolt_log', 'extra', $this->write(...));
         \Bolt\Doctrine\Migrations\ArrayToJsonMigrator::migrateDown($this->connection, 'bolt_log', 'user', $this->write(...));
         \Bolt\Doctrine\Migrations\ArrayToJsonMigrator::migrateDown($this->connection, 'bolt_log', 'location', $this->write(...));
     }
     ```
   - Rename index `field_translation_unique_translation` to `bolt_field_translation_unique_translation`:
     ```sql
     ALTER TABLE bolt_field_translation DROP FOREIGN KEY FK_5C60C0542C2AC5D3
     DROP INDEX field_translation_unique_translation ON bolt_field_translation
     CREATE UNIQUE INDEX bolt_field_translation_unique_translation ON bolt_field_translation (translatable_id, locale)
     ALTER TABLE bolt_field_translation ADD CONSTRAINT FK_5C60C0542C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bolt_field (id) ON DELETE CASCADE
     ```
   - Changed column type on `bolt_user.roles` and `bolt_field_translation.value` to native JSON type.
   - If the migration tries to drop the `bolt_password_request` table make sure to remove that action (both in down and up).
3. If any migrations where generated in the previous step, fully comment the code.
4. Run `bin/console doctrine:migrations:diff --from-empty-schema`
5. Prepend the following to the up action of the newly generated migration. Make sure that the table name matches your migration versions table.
   ```php
   if ($this->sm->tablesExist(['bolt_field'])) {
       $this->addSql('DELETE FROM doctrine_migration_versions');
       return;
   }
   // All other code of the migration
   ```
6. Rename the migration file to `Version00000000000000.php` and rename the class too. This ensures that it is run first and that migrations generated in step 2 are still being executed to fix your database state after upgrading.
7. Uncomment any migration from step 3 to restore their migration function.

This should work on your production environment as well when you are running the `doctrine:migrations:migrate` on deployment, but as always we recommend to make a backup before trying the upgrade.

## Interface updates

- `DetailControllerInterface::record` has been updated to include the request as parameter.

## Widget updates

The following widgets were replaced:

- `bobdenotter/weatherwidget` with `bolt/weatherwidget`
- `bobdenotter/configuration-notices` with `bolt/configuration-notices-widget`

Your configuration should be migrated automatically, but if you weren't using the configuration notices widget you might need to remove the newly added configuration manually.

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

## Removed deprecated implementations

These were deprecated in Bolt 5.1 and are now removed.

- `Bolt\Event\Listener\FieldFillListener::postLoad`
- `Bolt\Menu\CachedBackendMenuBuilder`
- `Bolt\Menu\CachedFrontendMenuBuilder`
- `Bolt\Menu\StopwatchBackendMenuBuilder`
- `Bolt\Menu\StopwatchFrontendMenuBuilder`
