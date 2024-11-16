install:
	composer install

lint:
	bin/console lint:container
	bin/console doctrine:schema:validate --skip-sync
	vendor/bin/ecs --fix
	vendor/bin/rector
	vendor/bin/phpstan

test:
	APP_ENV=test bin/console doctrine:database:drop -f
	APP_ENV=test bin/console doctrine:database:create
	APP_ENV=test bin/console doctrine:schema:create
	APP_ENV=test bin/console doctrine:fixtures:load -n
	bin/phpunit

sql:
	bin/console d:s:u --dump-sql --complete

up:
	docker compose up -d

reset:
	bin/console d:s:d -f
	bin/console d:s:c
	bin/console d:f:l -n

