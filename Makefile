install:
	composer install

up:
	docker compose up -d

down:
	docker compose down

clean:
	rm -rf docker/dev/postgres/data
	rm -rf docker/dev/nginx/log
	rm -rf vendor

lint:
	php bin/console lint:container
	php vendor/bin/ecs --fix
#	php vendor/bin/rector
	php vendor/bin/phpstan -v --memory-limit=1G

test:
	php vendor/bin/phpunit

db:
	php bin/console doctrine:schema:drop -f
	php bin/console doctrine:schema:create
	php bin/console doctrine:fixtures:load -n
