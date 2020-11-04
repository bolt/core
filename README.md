Bolt 4 - stable
=============

Bolt CMS is an open source, adaptable platform for building and running modern
websites. Built on PHP, Symfony and more. [Read the site](https://boltcms.io)
for more info.

To check out Bolt and set up your first Bolt installation, read
[Installing Bolt 4][installation].


---


**This repository should be used to work _<ins>on</ins>_ Bolt. Not
_<ins>with</ins>_ Bolt. If you want to check out Bolt, the
`composer create-project` method is recommended. See here:
https://github.com/bolt/project/ .**


---
> Cleanse this world with flame  
> End this, cleanse this  
> **Rebuild and start again**  
> Obliterate what makes us weak  

-- Hatebreed - [Destroy Everything][hatebreed]

Progress
------------------------------

Follow the progress on the development of Bolt 4, at these locations

 - Github Repository: https://github.com/bolt/core
 - Bolt 4 roadmap: https://roadmap.boltcms.io
 - Slack: https://slack.bolt.cm (open for all, requires Slack account)

To set up a running **development** environment of Bolt 4 please perform the following steps 1 to 4:

1. Install
----------

To install a _**development**_ version of Bolt 4:

  - Check out this git repository
  - Then run:

<!-- If you're reading this, and you're curious about the echo statement below:
We've found that people are _really_ terrible at reading. There's a big notice
on the top of this page, telling people the purpose of this repository, but
they miss it, and just paste the following on the CLI, assuming it's what they
need. -->

```bash
echo "\n\nAre you sure you shouldn't use https://docs.bolt.cm/installation instead?\n\n"
sleep 5
composer install
npm install && npm run build
```

Alternatively, run `make install`, on a UNIX-like system.

If you already have Bolt 4 installed and need to update dependencies run:
```bash
composer update
```

### Or install with Docker

To install a _**development**_ version of Bolt 4 with Docker:

  - Check out the git repo
  - Then run:

```bash
echo "\n\nAre you sure you shouldn't use https://docs.bolt.cm/installation instead?\n\n"
sleep 5
make docker-install
```

Actually, just add `docker-` prefix to any Make command and that's it!

When installed with Docker, in your browser go to `http://0.0.0.0:8088/` for the frontend, and to
`http://0.0.0.0:8088/bolt` for the Admin Panel.

2. Set up Database
------------------

  - Configure the database connection in `.env` or stick with the default
    SQLite, which should work out of the box.
  - Then run:

```bash
bin/console doctrine:database:create
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load -n
```

Alternatively, run `make db-create`, on a UNIX-like system.

Note: if you're using SQLite, ensure that `var/db/` is readable and writable to
you, as well as to the webserver users. The same applies to the file
`var/data/bolt.sqlite` if it already exists.

3. Re-set the Database
----------------------

This is a Bolt prototype in flux, so stuff can break, and you might want to reset the database to
the "factory settings". To re-set a database to the latest, with fresh
dummy-content run the following:

```
bin/console doctrine:schema:drop --force
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load -n
```

Alternatively, run `make db-reset`, on a UNIX-like system.

4 How to build assets
---------------------

To set up initially, run `npm install` to get the required dependencies /
`node_modules`. Alternatively give the path to the python executable (`npm install --python="/usr/local/bin/python3.7"`) Then:
  - Prepare directory structure `mkdir -p node_modules/node-sass/vendor`
  - Rebuild npm environment for current OS `npm rebuild node-sass`
  - Run `npm run start` (alternatively `npm run start --python="/usr/local/bin/python3.7"`)

See the other options by running `npm run`.
(Note: on testing remotely, assets can be compiled into public/assets by simply `npm run-script build`)

5. Run the prototype
--------------------

  - Using the Symfony CLI tool, just run `symfony server:start`.

  - Alternatively, run `bin/console server:start`
  (if running `bin/console server:start`does not work because you don't have the pcntl extension, run `bin/console server:run`)

In your browser, go to `http://127.0.0.1:8000/` for the frontend, and to
`http://127.0.0.1:8000/bolt` for the Admin Panel.

You can log on, using the default user & pass:

 - user: `admin`
 - pass: `admin%1`


Code Style checking / Static Analysis
-------------------------------------

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
- acceptance (UI, end-to-end): Behat/Mink

To run PHP unit tests:
```
make test
```

To run E2E tests:
```
make behat-js
```

Read more about running and creating tests on the [e2e tests page](tests/e2e/README.md).

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
-----------

The ongoing Bolt development takes place under the care of:

 - [Two Kings &ndash; Artisinal Web Development](https://twokings.nl)

Sponsors:

 - → You and/or your company's name on this list?
 [Become a sponsor](https://github.com/users/bobdenotter/sponsorship).

[fowler]: https://martinfowler.com/articles/practical-test-pyramid.html
[translations]: https://github.com/bolt/core/wiki/Contribute-on-translations
[hatebreed]: https://www.youtube.com/watch?v=DBwgX8yBqsw
[installation]: https://docs.bolt.cm/installation

--------

[![Build Status](https://travis-ci.com/bolt/core.svg?branch=master)](https://travis-ci.com/bolt/core) [![SymfonyInsight](https://insight.symfony.com/projects/4d1713e3-be44-4c2e-ad92-35f65eee6bd5/mini.svg)](https://insight.symfony.com/projects/4d1713e3-be44-4c2e-ad92-35f65eee6bd5) [![Total Downloads](https://poser.pugx.org/bolt/core/downloads)](https://packagist.org/packages/bolt/core) ![PHP from Packagist](https://img.shields.io/packagist/php-v/bolt/core)
