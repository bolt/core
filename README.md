Bolt 4.0.0 prototype
====================

> Cleanse this world with flame
> End this, cleanse this
> **Rebuild and start again**
> Obliterate what makes us weak

-- Hatebreed - [Destroy Everything](https://www.youtube.com/watch?v=DBwgX8yBqsw)

Progress towards alpha / beta
-----------------------------

Follow the progress on Bolt 4, at the following locations

 - Github Repository: https://github.com/bolt/four
 - Bolt 4 roadmap: http://bit.ly/bolt4-roadmap
 - Planboard: http://bit.ly/bolt4-board (open for all, requires Github Auth)

Install
-------

To install Bolt 4 (for now):

  - Check out the git repo
  - Then:
  
  ```bash
composer install
npm install && npm run build
  ```

Alternatively, run `make install`, on a UNIX-like system.

It's on the roadmap for Beta 1 to provide a `composer create-project` install.

Use with Docker
---------------

To install Bolt 4 with Docker (for now, on a UNIX-like system):

  - Check out the git repo
  - Then:

  ```bash
make docker-install
make docker-db-create
  ```

Actually, just add `docker-` prefix to any Make command and that's it!

In your browser, go to http://0.0.0.0:8088/ for the frontend, and to http://0.0.0.0:8088/bolt for the Admin Panel.

Set up Database
---------------

  - Configure the database connection in `.env`. Or stick with the default
    SQLite. It ought to work out of the box.
  - Then:

```bash
bin/console doctrine:database:create
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load -n
```

Alternatively, run `make db-create`, on a UNIX-like system.

Re-set the Database
-------------------

This is a prototype in flux. Shit will break, and you might want to reset it to
the "factory settings". To Re-set a database to the latest, with fresh
dummy-content use this:

```
bin/console doctrine:schema:drop --force
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load -n
```

Alternatively, run `make db-reset`, on a UNIX-like system.

Run the prototype
-----------------

  - Run `bin/console server:start`

In your browser, go to `http://127.0.0.1:8000/` for the frontend, and to
`http://127.0.0.1:8000/bolt` for the Admin Panel.

You can log on, using the default user & pass:

 - user: `admin`
 - pass: `admin%1`

Build assets
------------

To set up initially, run `npm install` to get the required dependencies /
`node_modules`. Then:

  - Run `npm run serve`

See the other options by running `npm run`.

Code Style / Static Analysis
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
---

Bolt uses several testing frameworks for [different test layers](https://martinfowler.com/articles/practical-test-pyramid.html):
- unit: PHPSpec, PHPUnit, Jest
- integration (of Symfony services): PHPUnit with KernelTestCase
- functional (API Contracts): Behat
- acceptance (UI, end-to-end): Kakunin

To run PHP unit tests:
```
make test
```

To run JS unit tests:
```
npm test
```

To run E2E tests:
```
make e2e
```

