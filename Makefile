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

seed_test_db:
	bin/console --env=test doctrine:fixtures:load

create_test_db:
	bin/console --env=test doctrine:database:create

migrate_test_db:
	bin/console --env=test doctrine:migrations:migrate

jwt_token:
	bin/console	lexik:jwt:generate-keypair --overwrite
