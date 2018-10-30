Bolt 4.0.0 prototype
====================

> Cleanse this world with flame  
> End this, cleanse this  
> **Rebuild and start again**  
> Obliterate what makes us weak  

-- Hatebreed - [Destroy Everything](https://www.youtube.com/watch?v=DBwgX8yBqsw)

Progress towards alpha / beta
-----------------------------

Is tracked on the project board: https://github.com/bolt/four/projects/1

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

To set up initially, run `npm install` to get the required dependencies /
`node_modules`. Then:

  - Run `npm run serve`

See the other options by running `npm run`.
