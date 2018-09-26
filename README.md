Bolt 4.0.0 prototype
====================

Install
-------

  - Check out the git repo
  - Run `composer install`

Set up Database
---------------

  - Configure the connection in `.env`
  - Then:

```
bin/console doctrine:database:create
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load -n
```

Re-set a database to the latest, with fresh dummy-content like this:

```
bin/console doctrine:schema:drop --force
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load -n
```

Run
---

  - Run `bin/console server:start`

Build assets
------------

To set up initially, run `yarn install` to get the required dependencies /
`node_modules`. Then:

  - Run `node_modules/.bin/encore dev --watch`

See the other options by running `node_modules/.bin/encore`.
