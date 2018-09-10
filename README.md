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
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load
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
