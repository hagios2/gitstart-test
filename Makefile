bash:
	docker exec -it php bash

create_db:
	bin/console doctrine:database:create

migrate:
	bin/console doctrine:migrations:migrate

seed:
	bin/console doctrine:fixtures:load
