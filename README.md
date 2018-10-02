Bolt 4.0.0 prototype
====================

Install
-------

  - Check out the git repo
  - Run `composer install`

Set up Database
---------------

  - Configure the database connection in `.env`. Or stick with the default 
    SQLite. It ought to work out of the box.
  - Then:

```
bin/console doctrine:database:create
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load -n
```

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

To set up initially, run `yarn install` to get the required dependencies /
`node_modules`. Then:

  - Run `yarn run watch`

See the other options by running `yarn run`.
