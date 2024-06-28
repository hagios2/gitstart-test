

To run test

php bin/console --env=test doctrine:fixtures:load


https://documenter.getpostman.com/view/6535328/2sA3duEsnx


docker exec -it php bash

bin/console doctrine:database:create

bin/console doctrine:migrations:migrate

[//]: # (create the default user in the db)
bin/console doctrine:fixtures:load  

[//]: # (get the login credentials)
use email: hagioswilson@gmail and password: password to login
