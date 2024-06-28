up: bash create_db migrate seed

test:
	bin/phpunit

bash:
	docker exec -it php bash

build:
	docker-compose up --build -d

create_db:
	bin/console doctrine:database:create

migrate:
	bin/console doctrine:migrations:migrate

seed:
	bin/console doctrine:fixtures:load

seed_test:
	bin/console doctrine:fixtures:load --env=test

create_test_db:
	bin/console doctrine:database:create

