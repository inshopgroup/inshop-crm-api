#!/bin/bash

composer install

php bin/console lexik:jwt:generate-keypair
php bin/console make:entity App --regenerate -n
php bin/console doctrine:database:drop --if-exists --force
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console doctrine:schema:validate
php bin/console doctrine:migration:migrate --no-interaction
php bin/console doctrine:fixtures:load --append
