#!/bin/bash

openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
openssl rsa -in config/jwt/private.pem -out config/jwt/private2.pem
mv config/jwt/private.pem config/jwt/private.pem-back
mv config/jwt/private2.pem config/jwt/private.pem

composer install

php bin/console make:entity App --regenerate -n
php bin/console doctrine:database:drop --if-exists --force
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console doctrine:schema:validate
php bin/console doctrine:migration:migrate --no-interaction
php bin/console doctrine:fixtures:load --append
