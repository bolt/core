Bolt 4 - beta
=============

> Cleanse this world with flame  
> End this, cleanse this  
> **Rebuild and start again**  
> Obliterate what makes us weak  

-- Hatebreed - [Destroy Everything][hatebreed]

Progress towards beta / stable 
------------------------------

Follow the progress on Bolt 4, at the following locations

 - Github Repository: https://github.com/bolt/four
 - Bolt 4 roadmap: https://roadmap.bolt.cm
 - Slack: https://slack.bolt.cm (open for all, requires Slack account)

**If you want to check out Bolt, the `composer create-project` method is
recommended. See here: https://github.com/bolt/project/ . This
repository should be used to work _on_ Bolt. Not _with_ Bolt.**

To set up a running environment of Bolt 4 please perform the following steps 1 to 4:

1 Install
---------

To install a development version of Bolt 4:

  - Check out this git repository
  - Then run:

  ```bash
composer install
npm install && npm run build
  ```

Alternatively, run `make install`, on a UNIX-like system.

### Or install with Docker

To install Bolt 4 with Docker (for now, on a UNIX-like system):

  - Check out the git repo
  - Then run:

  ```bash
make docker-install
  ```

Actually, just add `docker-` prefix to any Make command and that's it!

When installed with Docker, in your browser go to `http://0.0.0.0:8088/` for the frontend, and to
`http://0.0.0.0:8088/bolt` for the Admin Panel.

2 Set up Database
-----------------

  - Configure the database connection in `.env` or stick with the default
    SQLite, which should work out of the box.
  - Then run:

```bash
bin/console doctrine:database:create
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load -n
```

Alternatively, run `make db-create`, on a UNIX-like system.

3 Re-set the Database
---------------------

This is a Bolt prototype in flux, so stuff can break, and you might want to reset the database to
the "factory settings". To re-set a database to the latest, with fresh
dummy-content run the following:

```
bin/console doctrine:schema:drop --force
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load -n
```

Alternatively, run `make db-reset`, on a UNIX-like system.

4 Run the prototype
-------------------

  - Using the Symfony CLI tool, just run `symfony server:start`. 

  - Alternatively, run `bin/console server:start`
  (if running `bin/console server:start`does not work because you don't have the pcntl extension, run `bin/console server:run`)

In your browser, go to `http://127.0.0.1:8000/` for the frontend, and to
`http://127.0.0.1:8000/bolt` for the Admin Panel.

You can log on, using the default user & pass:

 - user: `admin`
 - pass: `admin%1`


How to build assets
-------------------

To set up initially, run `npm install` to get the required dependencies /
`node_modules`. Then:

  - Run `npm run start`

See the other options by running `npm run`.


Code Style checking / Static Analysis
----------------------------

Run the following commands with `make`, to perform Code Style checking and
automatic fixing:

 - `make cscheck`: Run ECS and PHPStan
 - `make csfix`: Run ECS, perform automatic fixes and run PHPStan

On windows, you can run the commands separately:

```bash
vendor/bin/ecs.bat check src
```

```bash
vendor/bin/ecs.bat check src --fix
```

```bash
vendor/bin/phpstan.bat analyse -c phpstan.neon src
```

Testing
-------

Bolt uses several testing frameworks for [different test layers][fowler]:
- unit: PHPSpec, PHPUnit, Jest
- integration (of Symfony services): PHPUnit with KernelTestCase
- functional (API Contracts): Behat
- acceptance (UI, end-to-end): Kakunin

To run PHP unit tests:
```
make test
```

To run E2E tests:
```
make e2e
```

Fixing IDE issues
-----------------

- PHPStorm does not see `@bolt` Twig namespace.

  The namespace needs to be added manually in Twig Plugin settings, pointing to `templates` folder.


Translations
------------

These are the translations used in Bolt. We rely on the community to expand on new
translations, and keep them up to date. If you wish to participate, read the
[instructions here][translations].

Several translation-related Console commands are available:

 - `bin/console translation:download` - Download translations from Loco (replaces all local ones)
 - `bin/console translation:sync bolt down` - Download translations from Loco (replaces local changes if there is a conflict)
 - `bin/console translation:sync bolt up` - Send updated translations to Loco

Development
--------

The ongoing Bolt development takes place under the care of:

 - [Two Kings &ndash; Artisinal Web Development](https://twokings.nl)
 - [The Software House &ndash; Web and Mobile Development](https://tsh.io/)

Sponsors: 

 - [Webforward, Richard Leishman](https://www.webfwd.co.uk/)
 - → You and/or your company's name on this list? 
 [Become a sponsor](https://github.com/users/bobdenotter/sponsorship).
 
 
[fowler]: https://martinfowler.com/articles/practical-test-pyramid.html
[translations]: https://github.com/bolt/four/wiki/Contribute-on-translations
[hatebreed]: https://www.youtube.com/watch?v=DBwgX8yBqsw
