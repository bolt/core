install:
	composer install
	npm install
	npm run build

start:
	make install
	make db-create
	make server

server:
	bin/console server:start

csclear:
	mkdir -p var/cache/ecs
	chmod -R a+rw var/cache/ecs
	rm -rf var/cache/ecs/*

cscheck:
	make csclear
	vendor/bin/ecs check src
	make stancheck

csfix:
	make csclear
	vendor/bin/ecs check src --fix
	make stancheck
	
stancheck:
	vendor/bin/phpstan analyse -c phpstan.neon src

db-create:
	bin/console doctrine:database:create
	bin/console doctrine:schema:create
	bin/console doctrine:fixtures:load -n

db-reset:
	bin/console doctrine:schema:drop --force
	bin/console doctrine:schema:create
	bin/console doctrine:fixtures:load -n
