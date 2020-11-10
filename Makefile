DC_RUN ?= docker-compose run --rm

SHELL = bash

COMPOSER ?= COMPOSER_MEMORY_LIMIT=-1 composer

.PHONY: help install server server-stop cache csclear cscheck csfix csfix-tests stancheck test \
behat behat-quiet behat-js behat-js-quiet behat-api behat-api-quiet full-test db-create db-update db-reset \
docker-install docker-install-deps docker-start docker-assets-serve \
docker-update docker-cache docker-csclear docker-cscheck docker-csfix docker-stancheck docker-db-create docker-db-reset \
docker-db-update docker-npm-fix-env docker-test docker-server-stop docker-behat docker-full-test \
docker-command docker-console

default: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?##.*$$' $(MAKEFILE_LIST) | sort | awk '{split($$0, a, ":"); printf "\033[36m%-30s\033[0m %-30s %s\n", a[1], a[2], a[3]}'

start: ## to run the install scripts and start the server
	make install
	make db-create
	make server

install: ## to install all project dependencies (Composer and NPM)
	$(COMPOSER) install
	npm install
	npm run build

update:
	$(COMPOSER) update && $(COMPOSER) outdated

server: ## to start server
	bin/console server:start 127.0.0.1:8088 -q || true

server-stop: ## to stop server
	bin/console server:stop

cache: ## to clean cache
	bin/console cache:clear

csclear: ## to clean cache and check coding style
	mkdir -p var/cache/ecs
	chmod -R a+rw var/cache/ecs
	rm -rf var/cache/ecs/*

cscheck: ## to check coding style
	make csclear
	vendor/bin/ecs check src
	vendor/bin/ecs check tests/spec
	vendor/bin/ecs check tests/php
	make stancheck

csfix: ## to fix coding style
	make csclear
	vendor/bin/ecs check src --fix
	vendor/bin/ecs check tests/spec --fix
	vendor/bin/ecs check tests/php --fix
	make stancheck

stancheck: ## to run phpstan
	vendor/bin/phpstan --memory-limit=1G analyse -c phpstan.neon src

test: ## to run phpunit tests
	vendor/bin/phpspec run
	vendor/bin/phpunit

behat-api: ## to run behat API tests
	make server
	vendor/bin/behat --tags=api

behat-api-quiet: ## to run behat API tests quietly
	make server
	vendor/bin/behat --tags=api --format=progress

behat-js: ## to run behat JS tests
	make server
	echo "Running Behat e2e tests. Make sure you have the latest version of Google Chrome installed"
	. ./run_behat_tests.sh
	## run the selenium server. chromedriver executable must be in $PATH
	vendor/bin/selenium-server-standalone >/dev/null 2>&1 &
	sleep 10s
	vendor/bin/behat --tags=javascript
	## @todo: stop selenium server

behat-js-quiet: ## to run behat JS tests quietly
	make server
	echo "Running Behat e2e tests. Make sure you have the latest version of Google Chrome installed"
	. ./run_behat_tests.sh
	## run the selenium server. chromedriver executable must be in $PATH
	vendor/bin/selenium-server-standalone >/dev/null 2>&1 &
	sleep 10s
	vendor/bin/behat --tags=javascript --format=progress
	## @todo: stop selenium server

behat:
	make behat-api
	make behat-js

behat-quiet:
	make behat-api-quiet
	make behat-js-quiet

behat-in-ci:
	make db-reset-without-images
	make behat-js-quiet

full-test: ## to run full tests
	make cscheck
	make test
	make behat

db-create: ## to create database and load fixtures
	bin/console doctrine:database:create
	bin/console doctrine:schema:create
	bin/console doctrine:fixtures:load -n

db-update: ## to update schema database
	bin/console doctrine:schema:update -v --dump-sql --force --complete

db-reset: ## to delete database and load fixtures
	bin/console doctrine:schema:drop --force --full-database
	bin/console doctrine:schema:create
	bin/console doctrine:fixtures:load -n

db-reset-without-images:
	bin/console doctrine:schema:drop --force --full-database
	bin/console doctrine:schema:create
	bin/console doctrine:fixtures:load --group=without-images -n

# Dockerized commands:
docker-install: ## to install project with docker
	make docker-start
	make docker-install-deps
	make docker-db-create

docker-install-deps: ## to install all assets with docker
	docker-compose exec -T php sh -c "$(COMPOSER) install"
	$(DC_RUN) node sh -c "npm install"
	$(DC_RUN) node sh -c "npm rebuild node-sass"
	$(DC_RUN) node sh -c "npm run build"

docker-build: ## to build containers
	docker-compose build

docker-start: ## to start containers
	docker-compose up -d

docker-assets-serve: ## to run server with npm
	$(DC_RUN) node sh -c "npm run serve"

docker-update: ## to update dependencies with docker
	docker-compose exec -T php sh -c "$(COMPOSER) update && $(COMPOSER) outdated"

docker-cache: ## to clean cache with docke
	docker-compose exec -T php sh -c "bin/console cache:clear"

docker-csclear: ## to clean cache and check coding style with docker
	docker-compose exec -T php sh -c "mkdir -p var/cache/ecs"
	docker-compose exec -T php sh -c "chmod -R a+rw var/cache/ecs"
	docker-compose exec -T php sh -c "rm -rf var/cache/ecs/*"

docker-cscheck: ## to check coding style with docker
	make docker-csclear
	docker-compose exec -T php sh -c "vendor/bin/ecs check src"
	make docker-stancheck

docker-csfix: ## to fix coding style with docker
	make docker-csclear
	docker-compose exec -T php sh -c "vendor/bin/ecs check src --fix"
	make docker-stancheck

docker-stancheck: ## to run phpstane with docker
	docker-compose exec -T php sh -c "vendor/bin/phpstan analyse -c phpstan.neon src"

docker-db-create: ## to create database and load fixtures with docker
	docker-compose exec -T php sh -c "bin/console doctrine:database:create"
	docker-compose exec -T php sh -c "bin/console doctrine:schema:create"
	docker-compose exec -T php sh -c "bin/console doctrine:fixtures:load -n"

docker-db-reset: ## to delete database with docker
	docker-compose exec -T php sh -c "bin/console doctrine:schema:drop --force --full-database"
	docker-compose exec -T php sh -c "bin/console doctrine:schema:create"
	docker-compose exec -T php sh -c "bin/console doctrine:fixtures:load -n"

docker-db-update: ## to update schema database with docker
	docker-compose exec -T php sh -c "bin/console doctrine:schema:update -v --dump-sql --force --complete"

docker-npm-fix-env: ## to rebuild asset sass
	$(DC_RUN) node sh -c "npm rebuild node-sass"

docker-test: ## to run phpspec and phpunit tests with docker
	docker-compose exec -T php sh -c "vendor/bin/phpspec run"
	docker-compose exec -T php sh -c "vendor/bin/phpunit"

docker-server: ## to start server with docker
	docker-compose exec -T php bin/console server:start 127.0.0.1:8088

docker-server-stop: ## to stop server with docker
	docker-compose exec -T -u www-data php bin/console server:stop

docker-behat: ## to run behat tests with docker
	docker-compose exec -T php vendor/bin/behat -v

docker-full-test: ## to run all test with docker
	make docker-cache
	make docker-cscheck
	make docker-test
	make docker-behat
	make behat

docker-command: ## to run commmand shell in php container
	docker-compose exec -T php sh -c "$(c)"

docker-console: ## to run commmand with console symfony in php container
	docker-compose exec -T php sh -c "bin/console $(c)"
