start:
	make install
	make db-create
	make server

install:
	composer install
	npm install
	npm run build

server:
	bin/console server:start 127.0.0.1:8088 || true

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

behat:
	make server
	vendor/bin/behat -v

behat-rerun:
	make server
	vendor/bin/behat -v --rerun

e2e:
	make server
	cd tests/e2e && npm run kakunin && cd ../..

full-test:
	make cscheck
	make test
	npm test
	make behat
	make e2e

e2e-wip:
	make server
	cd tests/e2e && npm run kakunin -- --tags @wip && cd ../..

e2e-install:
	cd tests/e2e
	npm install
	node ./node_modules/protractor/bin/webdriver-manager update --gecko=false
	cd step_definitions
	ln -s ../node_modules/kakunin/dist/step_definitions/elements.js kakunin-elements.js
	ln -s ../node_modules/kakunin/dist/step_definitions/debug.js kakunin-debug.js
	ln -s ../node_modules/kakunin/dist/step_definitions/file.js kakunin-file.js
	ln -s ../node_modules/kakunin/dist/step_definitions/form.js kakunin-form.js
	ln -s ../node_modules/kakunin/dist/step_definitions/email.js kakunin-email.js
	ln -s ../node_modules/kakunin/dist/step_definitions/generators.js kakunin-generators.js
	ln -s ../node_modules/kakunin/dist/step_definitions/navigation.js kakunin-navigation.js
	cd ../../..

db-create:
	bin/console doctrine:database:create
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
	cp -n .env.dist .env
	docker-compose up -d
	docker-compose exec -T php sh -c "composer install"
	docker-compose run node sh -c "npm install"
	docker-compose run node sh -c "npm run build"
	make docker-db-create

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

docker-test:
	docker-compose exec -T php sh -c "vendor/bin/phpunit"
	docker-compose exec -T php sh -c "vendor/bin/phpspec run"

docker-behat:
	docker-compose exec -T php vendor/bin/behat -v

docker-behat-rerun:
	docker-compose exec -T php vendor/bin/behat -v --rerun

docker-full-test:
	make docker-cache
	make docker-cscheck
	make docker-test
	npm test
	make docker-behat
	make e2e
