#!/bin/bash

docker-compose exec --user=www-data php openssl genrsa -out config/jwt/private.pem -aes256 4096
docker-compose exec --user=www-data php openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

docker-compose exec --user=www-data php composer install

docker-compose exec --user=www-data php php bin/console make:entity App --regenerate -n
docker-compose exec --user=www-data php php bin/console doctrine:database:drop --if-exists --force
docker-compose exec --user=www-data php php bin/console doctrine:database:create
docker-compose exec --user=www-data php php bin/console doctrine:schema:update --force
docker-compose exec --user=www-data php php bin/console doctrine:schema:validate
docker-compose exec --user=www-data php php bin/console doctrine:migration:migrate --no-interaction
docker-compose exec --user=www-data php php bin/console doctrine:fixtures:load --append
