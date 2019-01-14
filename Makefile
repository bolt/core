start:
	make install
	make db-create
	make server

install:
	composer install
	npm install
	npm run build

server:
	bin/console server:start 127.0.0.1:8088

cache:
	bin/console cache:clear

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

csfix-tests:
	make csclear
	vendor/bin/ecs check tests/php --fix
	make stancheck
	
stancheck:
	vendor/bin/phpstan --memory-limit=1G analyse -c phpstan.neon src

test:
	vendor/bin/phpunit
	vendor/bin/phpspec run

e2e:
	make server
	cd tests/e2e && npm run kakunin && cd ../..

db-create:
	bin/console doctrine:database:create --if-not-exists
	bin/console doctrine:schema:create
	bin/console doctrine:fixtures:load -n

db-reset:
	bin/console doctrine:schema:drop --force
	bin/console doctrine:schema:create
	bin/console doctrine:fixtures:load -n

# Dockerized commands:
docker-start:
	make docker-install
	make docker-db-create

docker-install:
	docker-compose up -d
	docker-compose exec -T php sh -c "composer install"
	docker-compose run node sh -c "npm install"
	docker-compose run node sh -c "npm run build"

docker-update:
	docker-compose exec -T php sh -c "composer update"

docker-cache:
	docker-compose exec -T php sh -c "bin/console cache:clear"

docker-csclear:
	docker-compose exec -T php sh -c "mkdir -p var/cache/ecs"
	docker-compose exec -T php sh -c "chmod -R a+rw var/cache/ecs"
	docker-compose exec -T php sh -c "rm -rf var/cache/ecs/*"

docker-cscheck:
	make docker-csclear
	docker-compose exec -T php sh -c "vendor/bin/ecs check src"
	make docker-stancheck

docker-csfix:
	make docker-csclear
	docker-compose exec -T php sh -c "vendor/bin/ecs check src --fix"
	make docker-stancheck

docker-stancheck:
	docker-compose exec -T php sh -c "vendor/bin/phpstan analyse -c phpstan.neon src"

docker-db-create:
	docker-compose exec -T php sh -c "bin/console doctrine:database:create --if-not-exists"
	docker-compose exec -T php sh -c "bin/console doctrine:schema:create"
	docker-compose exec -T php sh -c "bin/console doctrine:fixtures:load -n"

docker-db-reset:
	docker-compose exec -T php sh -c "bin/console doctrine:schema:drop --force"
	docker-compose exec -T php sh -c "bin/console doctrine:schema:create"
	docker-compose exec -T php sh -c "bin/console doctrine:fixtures:load -n"

docker-npm-fix-env:
	docker-compose run node sh -c "npm rebuild node-sass"
